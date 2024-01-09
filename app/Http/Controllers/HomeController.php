<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index() {
        return view('home');
    }

    public function jsonSave(Request $request) {

        try {
            
            $jsonData = $request->except('image');
            
            // Handle image upload
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $imagePath = $image->storeAs('uploads/images', $imageName, 'public');

            // move and create images
            $targetDir = 'uploads/images/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
        
            //set image to unique name
            $new_name = $targetDir. $imageName;
            move_uploaded_file($request->image,  $new_name);
            $jsonData['image'] = $new_name;

            // Save JSON data to a file
            $jsonFilePath = storage_path('app/data.json');
            $existingData = json_decode(file_get_contents($jsonFilePath), true) ?? [];

            // Append new data to existing data
            $allData = array_merge($existingData, [$jsonData]);

            // Save the updated JSON data
            file_put_contents($jsonFilePath, json_encode($allData, JSON_PRETTY_PRINT));

            return response()->json(['message' => 'Data stored successfully']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => $th->getMessage()]);

        }
    }

    public function fetchData() {
        $data = file_get_contents(storage_path('app/data.json'));
        return $data;
    }
}
