<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\FileCheckMD5Request;
use App\Http\Requests\Api\V1\FileDownloadRequest;
use App\Http\Requests\Api\FileUploadRequest;
use App\Models\File;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SoareCostin\FileVault\Facades\FileVault;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Store a user uploaded file
     *
     * @param FileUploadRequest $request
     * @return Response|void|array
     */
    public function store(FileUploadRequest $request)
    {
        try {
            $user = auth()->user();

            if (!$user->hasRole('Super Admin Dau Kat Moj')) {
                return $this->contentErrorMessage('You are not allowed to upload files');
            }

            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                $product = Product::where('name', $request->product_name)->first();

                if (!$product) {
                    return $this->contentErrorMessage('Product not found');
                }

                $fileUpload = $request->file('file');
                $filePath = 'files/' . $product->slug . '/' . $product->version;
                $fileName = Carbon::now()->format('YYYYmmdd') . '_' . $fileUpload->getClientOriginalName();

                Storage::disk('local')->putFileAs(
                    $filePath,
                    $fileUpload,
                    $fileName
                );

                Log::info('Upload ' . $fileName . ' to ' . $filePath . ': OK!!!');

                if ($fileName) {
                    FileVault::encrypt($filePath . '/' . $fileName);

                    Log::info("File encrypted: OK!!!");

                    $fileModel = new File();
                    $fileModel->name = $request->file_name;
                    $fileModel->path = $filePath;
                    $fileModel->local_name = $fileName . '.enc';
                    $fileModel->original_name = $fileName;
                    $fileModel->size = $fileUpload->getSize();
                    $fileModel->mime_type = $fileUpload->getMimeType();
                    $fileModel->md5 = Hash::make(Str::lower($request->file_md5));
                    $fileModel->product()->associate($product->uuid);
                    $fileModel->save();

                    Log::info("File DB: OK!!!");

                    return $this->response->created($fileName);
                }
            }

            return $this->response->errorBadRequest();
        } catch (Exception $exception) {
            Log::error('Exception throw when handle access log with code: ' . $exception->getCode());
            Log::error('Detail: ' . $exception->getMessage());

            return $this->response->error($exception->getMessage(), 500);
        }
    }

    /**
     * Download a file
     *
     * @param FileDownloadRequest $request
     * @return StreamedResponse|void|array
     */
    public function downloadFile(FileDownloadRequest $request)
    {
        try {

            $user = auth()->user();

            $version = $request->product_version;
            $file_name = $request->file_name;

            Log::info($user->name . ' is trying download file ' . $file_name . ' from product ' . $request->product_name . ' with version ' . $version);

            if (!$user->hasPermissionTo('download file')) {
                Log::error($user->name . ' is not allowed to download file ' . $file_name . ' from product ' . $request->product_name . ' with version ' . $version);
                return $this->contentErrorMessage('You are not allowed to download files');
            }

            $product = Product::where('name', $request->product_name)->where('version', $version)->firstOrFail();
            $file = $product->files()->where('name', $file_name)->latest()->firstOrFail();

            if ($file) {
                $filePath = $file->path . '/' . $file->local_name;
                $fileName = $file->original_name;

                if (Storage::disk('local')->exists($filePath)) {

                    Log::info($fileName . ' exists in ' . $filePath);

                    return response()->streamDownload(function () use ($filePath) {
                        FileVault::streamDecrypt($filePath);
                    }, $fileName);
                }
            }

            return $this->contentErrorMessage("File doesn't exist");
        } catch (Exception $exception) {
            Log::error('Exception throw when handle download file with code: ' . $exception->getCode());
            Log::error('Detail: ' . $exception->getMessage());

            return $this->response->errorInternal();
        }
    }

    /**
     * Check MD5 of a file
     *
     * @param FileCheckMD5Request $request
     * @return Response|void|array
     */
    public function checkMD5File(FileCheckMD5Request $request)
    {
        try {
            if (!auth()->user()->hasPermissionTo('check md5 file')) {
                return $this->response->array(self::contentErrorMessage('You are not allowed to download files'));
            }

            Log::info(auth()->user()->name . ' is trying check MD5 of file ' . $request->file_name . ' from product ');

            $file = File::where('name', $request->file_name)->latest()->firstOrFail();

            if (!$file) {
                return $this->contentErrorMessage('File not found');
            }

            if (Hash::check(Str::lower($request->file_md5), $file->md5)) {
                return $this->response->array(self::contentSuccessMessage('File is valid', 200));
            }

            return $this->contentErrorMessage("File is not valid");
        } catch (Exception $exception) {
            Log::error('Exception throw when handle download file with code: ' . $exception->getCode());
            Log::error('Detail: ' . $exception->getMessage());

            return $this->response->errorInternal();
        }
    }

}
