<?php

namespace App\Services;

use App\Exceptions\Handler;
use App\Jobs\S3Delete;
use Illuminate\Support\Facades\Log;
use App\Jobs\S3HandleJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Storage;
use Image;
use Intervention\Image\Exception\NotReadableException;


/**
 * Class CustomS3
 * @package App\Services
 */
class CustomS3
{
    use DispatchesJobs;

    /**
     * CustomS3 constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $file
     * @param string $for
     * @param null $deleteImage
     * @return string
     * @throws \Exception
     */
    public function imageUploaderS3($file, $for = 'file', $deleteImage = null)
    {
        $imageName = random_int(99999, 999999999) . '_' . time() . '_img.jpeg';

        S3HandleJob::dispatch($imageName, $for, $deleteImage, $file)->delay(now()->addSeconds(1));

        if ($deleteImage) {
            // S3HandleJob::dispatch(null, $for, $deleteImage, null)->delay(now()->addSeconds(1));
            S3Delete::dispatchNow($for, $deleteImage);
        }

        return $imageName;
    }

    public function uploadDirect($file, $for = 'file', $deleteImage = null)
    {

        // $imageName = random_int(99999, 999999999) . '_' . time() . '_file.'.$file->extension();

//        $original = Image::make($file)->resize(750, 975, function ($constraint) {
//            $constraint->aspectRatio();
//        })->encode('jpeg', 100);


        // original
        $res = Storage::disk(env('FILESYSTEM_DRIVER'))->put($for . '/original/', $file);
        Storage::disk(env('FILESYSTEM_DRIVER'))->setVisibility($for . '/original/', 'public');


        if ($deleteImage) {
            $this->deleteDirect($for, $deleteImage);
        }

        return $res;
    }

    function deleteDirect($for, $deleteImage)
    {
        Storage::disk(env('FILESYSTEM_DRIVER'))->delete($for . '/original/' . $deleteImage);
    }


}
