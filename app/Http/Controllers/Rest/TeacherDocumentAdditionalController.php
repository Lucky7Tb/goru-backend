<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Teacher\AddDocumentRequest;
use App\Exceptions\NotAcceptableException;
use App\Models\TeacherDocumentAdditional;
use App\Exceptions\NotFoundException;
use Kreait\Firebase\Contract\Storage;
use App\Http\Controllers\Controller;
use App\Firebase\FirebaseStorage;
use Illuminate\Http\Request;

class TeacherDocumentAdditionalController extends Controller
{
    private $firebaseStorage;

    public function __construct(Storage $storage) {
        $this->firebaseStorage = new FirebaseStorage($storage);
    }

    public function getDocument()
    {
        $teacherDocuments = TeacherDocumentAdditional::select('id', 'document')
            ->where('user_id', '=', auth()->user()->id)
            ->get();

        foreach ($teacherDocuments as $teacherDocument) {
            $teacherDocument->document = "https://firebasestorage.googleapis.com/v0/b/goru-ee0f3.appspot.com/o/additional_documents%2F$teacherDocument->document?alt=media";
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil dokumen anda',
            'data' => $teacherDocuments
        ], 200);
    }

    public function addDocument(AddDocumentRequest $request)
    {

        $teacherDocuments = TeacherDocumentAdditional::select('id')
            ->where('user_id', '=', auth()->user()->id)
            ->count();

        if($teacherDocuments == 3) {
            throw new NotAcceptableException('Maximun dokumen portofolio adalah 3');
        }

        $document = $request->file('document');
        $documentFileName = $this->firebaseStorage->uploadFile($document, 'additional_documents/');
        TeacherDocumentAdditional::create([
            'user_id' => auth()->user()->id,
            'document' => $documentFileName
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Sukses menambahkan dokumen portofolio anda'
        ], 200);
    }

    public function deleteDocument(string $documentId)
    {
        $teacherDocument = TeacherDocumentAdditional::select('id', 'document')
            ->find($documentId);

        if (is_null($teacherDocument)) {
            throw new NotFoundException('Dokumen tidak ditemukan');
        }

        $teacherDocument->delete();
        $this->firebaseStorage->deleteFile($teacherDocument->document, 'additional_documents/');

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus dokumen portofoliomu'
        ], 200);
    }
}
