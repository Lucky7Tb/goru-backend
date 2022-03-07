<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Level\LevelRequest;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Level;

class LevelController extends Controller
{
    public function getAllLevel()
    {
        $levels = Level::select(['id', 'name', 'description'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Sukses mengambil level mengajar guru',
            'data' => $levels
        ], 200);
    }

    public function getOneLevel($levelId)
    {
        $level = Level::select(['id', 'name', 'description'])->find($levelId);
        if (is_null($level)) throw new NotFoundException('Level tidak ditemukan');

        return response()->json([
            'message' => 'Sukses mengambil level mengajar guru',
            'data' => $level
        ], 200);
    }

    public function createLevel(LevelRequest $levelRequest)
    {
        $levelData = $levelRequest->validated();
        Level::create($levelData);
        return response()->json([
            'status' => 201,
            'message' => 'Berhasil menambah level mengajar guru'
        ], 201);
    }

    public function updateLevel(LevelRequest $levelRequest, $levelId)
    {
        $level = Level::select(['id', 'name', 'description'])->find($levelId);
        if (is_null($level)) throw new NotFoundException('Level tidak ditemukan');

        $updatedLevelData = $levelRequest->validated();
        $level->name = $updatedLevelData['name'];
        $level->description = $updatedLevelData['description'];
        $level->save();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah level mengajar guru'
        ], 200);
    }

    public function deleteLevel($levelId)
    {
        $level = Level::select('id', 'name', 'description')->find($levelId);
        if (is_null($level)) throw new NotFoundException('Level tidak ditemukan');
        $level->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus level mengajar guru'
        ], 200);
    }
}
