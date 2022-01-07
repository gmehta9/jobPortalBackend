<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use  \App\Models\{Quotation, QuotationItem, QuotationItemImage, User};
use Illuminate\Support\Facades\Mail;


/**
 *
 */
class QuotationController extends BaseController
{
    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|int|mixed
     */
    private function generateQuoteId()
    {
        // To Check Quote Exist Or Not(Generate QuoteId like QU-0001)
        $userQuoteExist = Quotation::orderBy('quote_id', 'desc')->first();
        if ($userQuoteExist) {
            $quote_id = preg_replace('/[^0-9.]+/', '', $userQuoteExist->quote_id);
            $new_quote_id = (int)$quote_id + 1;
        } else {
            $new_quote_id = config('app.defaultQuoteIdStart');
        }
        return $new_quote_id;
    }

    /**
     * @param $request
     * @param Quotation $quotation
     * @return \Illuminate\Http\JsonResponse|void
     */
    function attachItemsInQuotation($request, Quotation $quotation)
    {

        try {

            $quotationItems = $request->input(['quotation']);

            foreach ($quotationItems as $Items) {
                $quoteItem = $quotation->quotationItems()->create([
                    'quotation_id' => $quotation['id'],
                    'width_feet' => $Items['width_feet'],
                    'width_inch' => $Items['width_inch'],
                    'width_fraction1' => $Items['width_fraction1'],
                    'width_fraction2' => $Items['width_fraction2'],
                    'type' => $Items['type'],
                    'height_feet' => $Items['height_feet'],
                    'height_inch' => $Items['height_inch'],
                    'height_fraction1' => $Items['height_fraction1'],
                    'height_fraction2' => $Items['height_fraction2'],
                    'description' => $Items['description'],
                    'tax' => $Items['tax'],
                    'is_taxable' => $Items['is_taxable'],
                    'price' => $Items['price'],
                    'quantity' => $Items['quantity'],
                    'discount' => $Items['discount'],
                    'square_feet' => $Items['square_feet'],
                    'total_price' => $Items['total_price'],
                    'user_id' => $quotation['user_id'],
                    'quote_id' => $quotation['quote_id'],
                ]);

                // Save image also
                if (isset($Items['images'])) {
                    foreach ($Items['images'] as $image) {
                        QuotationItemImage::insert(['image' => $image, 'quotation_item_id' => $quoteItem->id]);
                    }
                }
            }
        } catch (\Throwable $t) {
            return $this->sendMessageResponse('please Check your data format.');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'sub_total' => 'required',
            'tax' => 'required',
            'total' => 'required',
            'quotation' => 'required',
            'customer_id' => 'required|exists:users,id'

        ]);

        //To Check Quote Exist Or Not(Generate QuoteId like QU-0001)
        $userQuoteExist = Quotation::orderBy('quote_id', 'desc')->first();
        if ($userQuoteExist) {
            $quote_id = preg_replace('/[^0-9.]+/', '', $userQuoteExist->quote_id);
            $new_quote_id = (int)$quote_id + 1;
        } else {
            $new_quote_id = config('app.defaultQuoteIdStart');
        }

        $quotation_data = $request->only(['tax', 'sub_total', 'total', 'customer_id']);
        $quotation_data['user_id'] = $request->user()->id;
        $quotation_data['quote_id'] = $new_quote_id;
        $quotation_data['status'] = $request->exists('status') ? $request['status'] : 'onPublish';
        $quotation = Quotation::create($quotation_data);

        // attach items in quotation
        $this->attachItemsInQuotation($request, $quotation);

        $data = Quotation::with('users', 'items')->findOrFail($quotation->id);
        return $this->sendResponse($data, 'Quotation Created Successfully.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quotationhistory(Request $request)
    {
        $data = Quotation::with(['quotationItem' => function ($q) {
            $q->select('id', 'quotation_id', 'user_id', 'width_feet', 'width_inch', 'width_fraction1', 'width_fraction2', 'type', 'height_feet', 'height_inch', 'height_fraction1', 'height_fraction2', 'description', 'quote_id', 'tax', 'price', 'quantity', 'discount','square_feet');
        }, 'Customer' => function ($q) {
            $q->select('id', 'name', 'email', 'address', 'phone_number', 'company_name');
        }])->where('user_id', $request->user()->id)->select('id', 'customer_id', 'total', 'sub_total', 'tax', 'status')->get();

        if ($request->status == 'onPublish') {
            $data = $data->where('status', 'onPublish');

            return $this->sendResponse($data);
        }
        if ($request->status == 'onDraft') {
            $data = $data->where('status', 'onDraft');

            return $this->sendResponse($data);
        }

        return $this->sendResponse($data);
    }

    /**
     * @param Request $request
     * @param $quotation_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $quotation_id)
    {
        $quotation = Quotation::where('id', $quotation_id)->FirstOrFail();

        $toUpdate = $request->only(['tax', 'total', 'sub_total', 'customer_id']);
        $toUpdate['status'] = $request->exists('status') ? $request['status'] : 'onPublish';


        $quotation->update($toUpdate);

        if ($request->exists('quotation')) {

            // first we delete all old items
            QuotationItem::where('quotation_id', $quotation_id)->delete();

            // attach items in quotation
            $this->attachItemsInQuotation($request, $quotation);

        }
        return $this->sendResponse(Quotation::with('users', 'items')->findOrFail($quotation_id), 'Quotation Updated Successfully');

    }

    /**
     * @param $quotation_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($quotation_id)
    {
        $data = Quotation::where('id', $quotation_id)->FirstOrFail();

        $data->delete();

        return $this->sendMessageResponse('Quotation Deleted Successfully');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAll(Request $request)
    {
        $id = $request->user()->id;

        Quotation::where('user_id', $id)->delete();

        return $this->sendMessageResponse('All Quotation Deleted Successfully');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newQuoteId(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:users,email',
            'name' => 'required',
            'role' => 'required',
        ]);

        $inputs = $request->only(['name', 'email', 'address', 'company_name', 'phone_number', 'role', 'exempt']);
        $user = User::create($inputs);

        $quotation_data['user_id'] = $request->user()->id;
        $quotation_data['quote_id'] = $this->generateQuoteId();
        $quotation = Quotation::create($quotation_data);

        return $this->sendResponse(['user' => $user, 'quoteDetails' => $quotation]);
    }

    /**
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function quoteIdUpdate(Request $request, $user_id)
    {
        $user = User::where('id', $user_id)->FirstOrFail();
        $inputs = $request->only(['name', 'email', 'address', 'company_name', 'phone_number', 'role', 'exempt']);
        $user->update($inputs);

        $quotationData = Quotation::where('user_id', $user_id)->first();

        return $this->sendResponse(['user' => $user, 'quoteDetails' => $quotationData]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendQuotation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $cc_email = $request->input('cc_email');
        $request->input('notes');

        $data = $request->quote_id;
        $quote_id = preg_replace('/[^0-9.]+/', '', $data);

        $price = Quotation::where('quote_id', $quote_id)->first();

        if ($price) {
            Mail::send('SendQuote', [
                'name' => $request->user()->name,
                'quote_id' => $request->quote_id,
                'price' => $price->total,
            ],
                function ($message) use ($email, $cc_email) {
                    if (!empty($cc_email)) {
                        $message->to($email)->cc($cc_email)
                            ->subject('Quote Email');
                    }
                    $message->to($email)
                        ->subject('Quote Email');
                });
            return $this->sendMessageResponse('Mail Send Successfully.');

        } else {
            return $this->sendMessageResponse('Quote_id does not exist.', 404);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImages(Request $request)
    {
        $quotationItem = $request->quotation_item_id;

        $images = QuotationItemImage::where('quotation_item_id', $quotationItem)->pluck('image');

        foreach ($images as $image) {

            (new \App\Services\CustomS3())->deleteDirect('file', $image);

        }
        return $this->sendMessageResponse('Images Deleted Successfully.');
    }

    /**
     * @param $quote_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string
     */
    public function viewQuote($quote_id)
    {

        $quotation = Quotation::where('quote_id', $quote_id)->first();

        if ($quotation) {
            return view('viewQuote', compact('quotation'));
        } else {
            return 'Quote Id does not exist';
        }
    }

    /**
     * @param $quotationItem_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteQuotationItem($quotationItem_id)
    {
        $data = QuotationItem::where('id', $quotationItem_id)->FirstOrFail();

        $data->delete();

        return $this->sendMessageResponse('Quotation Item Deleted Successfully');
    }

    /**
     * @param $quotation_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function singleQuotation($quotation_id)
    {
        $data = Quotation::with('items')->where('id', $quotation_id)->FirstOrFail();
        return $this->sendResponse($data);
    }
}
