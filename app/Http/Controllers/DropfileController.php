<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Dropbox\Client;
use App\Dropfile;

class DropfileController extends Controller
{
    public function __construct()
    {
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
    }

    public function index()
    {
        $files = Dropfile::all();
        return view('pages.dropfile.index', compact('files'));
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'file' => 'required|max:2048'
        // ]);

        try {
            if ($request->hasFile('image')) {
                $files = $request->file('image');

                foreach ($files as $file) {
                    $fileExtension = $file->getClientOriginalExtension();
                    $mimeType = $file->getClientMimeType();
                    $fileSize = $file->getClientSize();
                    $newName = uniqid() . '.' . $fileExtension;

                    Storage::disk('dropbox')->putFileAs('public/upload', $file, $newName);
                    $this->dropbox->createSharedLinkWithSettings('public/upload/' . $newName);

                    Upload::create([
                        'file'  => $newName,
                        'type'  => $mimeType,
                        'size'  => $fileSize
                    ]);
                }

                return "Message: File(s) has been uploaded";
            }
        } catch (\Exception $e) {
            return "Message: {$e->getMessage()}";
        }
    }

    public function show($file)
    {
        try {
            $link = $this->dropbox->listSharedLinks('public/upload/' . $file);
            $raw = explode("?", $link[0]['url']);
            $path = $raw[0] . '?raw=1';
            $tempPath = tempnam(sys_get_temp_dir(), $path);

            $copy = copy($path, $tempPath);
            return response()->file($tempPath);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function download($file)
    {
        try {
            return Storage::disk('dropbox')->download('public/upload/' . $file);
        } catch (Exception $e) {
            return abort(404);
        }
    }

    public function destroy(Dropfile $dropfile)
    {
        try {
            Storage::disk('dropbox')->delete('public/upload/' . $upload->path);
            return $upload->delete();
        } catch (Exception $e) {
            return abort(404);
        }
    }
}
