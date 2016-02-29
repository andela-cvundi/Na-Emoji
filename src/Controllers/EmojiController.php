<?php

namespace Vundi\NaEmoji\Controllers;

use Vundi\NaEmoji\Models\Emoji;
use PDOException;
use Vundi\Potato\Exceptions\NonExistentID;
use Vundi\Potato\Exceptions\IDShouldBeNumber;

class EmojiController
{
    public static function All()
    {
        $emojis = Emoji::findAll();

        return $emojis;
    }

    public static function find($id)
    {
        $id = (int)$id;
        try {
            $emoji = Emoji::find($id);

            return $emoji->db_fields;
        } catch (NonExistentID $e) {
            return $message = [
                'emoji'   => 0,
                'message' => $e->getMessage(),
            ];
        } catch (IDShouldBeNumber $e) {
            return $message = [
                'emoji'   => 0,
                'message' => $e->getMessage(),
            ];
        }
    }

    public static function newEmoji($data)
    {
        $emoji = new Emoji();
        $emoji->name = $data['name'];
        $emoji->char = $data['char'];
        $emoji->keywords = $data['keywords'];
        $emoji->category = $data['category'];
        $emoji->date_created = date("Y-m-d H:i:s");
        $emoji->date_updated = date("Y-m-d H:i:s");
        $emoji->save();

        return $emoji;
    }


    public static function updateEmoji($id, $data)
    {
        try {

            $emoji = Emoji::find($id);
            $emoji->name = $data['name'];
            $emoji->char = $data['char'];
            $emoji->keywords = $data['keywords'];
            $emoji->category = $data['category'];
            $emoji->date_created = date("Y-m-d H:i:s");
            $emoji->date_updated = date("Y-m-d H:i:s");
            $emoji->update();

            return $message = [
                'success' => true
            ];

        } catch (NonExistentID $e) {
            return $message = [
                'emoji'   => 0,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
