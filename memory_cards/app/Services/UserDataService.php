<?php

namespace App\Services;

use App\Models\Groups;
use App\Models\Langs;
use App\Models\MemoryCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Export / import authenticated user data as JSON snapshot on the local disk.
 */
class UserDataService
{
    public function import(int $userId): bool
    {
        $raw = Storage::disk('local')->get($this->fileName($userId));
        if ($raw === null) {
            return false;
        }

        $jsonData = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        DB::beginTransaction();
        try {
            User::where('id', $jsonData['user']['id'])->delete();
            $newUserId = User::insertGetId($jsonData['user']);
            $userData = ['user_id' => $newUserId];

            foreach ($jsonData['langs'] as $lang) {
                Langs::insert(array_merge($lang, $userData));
            }
            foreach ($jsonData['groups'] as $group) {
                Groups::insert(array_merge($group, $userData));
            }
            foreach ($jsonData['memory_cards'] as $card) {
                MemoryCard::insert(array_merge($card, $userData));
            }

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return false;
        }
    }

    public function export(int $userId): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $userData = [
            'user'         => array_merge(['password' => $user->password], $user->toArray()),
            'langs'        => Langs::where('user_id', $userId)->get()->toArray(),
            'groups'       => Groups::where('user_id', $userId)->get()->toArray(),
            'memory_cards' => MemoryCard::where('user_id', $userId)->get()->toArray(),
        ];

        return Storage::disk('local')->put(
            $this->fileName($userId),
            json_encode($userData)
        );
    }

    private function fileName(int $userId): string
    {
        return 'user_' . $userId . '.json';
    }
}
