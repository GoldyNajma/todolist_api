<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
	const USER_STORAGE_PATH = '/uploads/user/';
	const IMAGE_KEY = 'image';

    private function storeFile(Request $request, $destinationPath, $fileKey) 
    {
		if ($request->hasFile($fileKey)) {
			$file = $request->file($fileKey);
			$originalFileName = $file->getClientOriginalName();
			$originalFileExtension = $file->getClientOriginalExtension();
			$fileName = time() . '.' . $originalFileExtension;
			
			if ($file->move('.' . $destinationPath, $fileName)) {
				$filePath = $destinationPath . $fileName;

				return response()->json([
					'path' => $filePath,
					'message' => 'File \''. $originalFileName . '\'' 
								 . ' uploaded successfully',
				], 201);
			} else {
				return response()->json([
					'message' => 'File could not be uploaded'
				], 500);
			}
		} else {
			return response()->json([
				'message' => 'File not found'
			], 400);
		} 
   	}

	public function storeUserImage(Request $request)
	{
		return $this->storeFile($request, self::USER_STORAGE_PATH, self::IMAGE_KEY);
	}
}
