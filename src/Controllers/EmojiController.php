<?php

namespace Vundi\NaEmoji\Controllers;

use Vundi\NaEmoji\Models\Emoji;
use PDOException;
use Vundi\Potato\Exceptions\NonExistentID;

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
        $emoji = Emoji::find($id);
        $emoji->FName = $data['FName'];
        $emoji->LName = $data['LName'];
        $emoji->Gender = $data['Gender'];
        $emoji->Age = $data['Age'];
        $emoji->update();
    }

    public static function delete($id)
    {
        try {
            $id = (int)$id;
            if (is_Object(Emoji::find($id))) {
                Emoji::remove($id);
                echo json_encode(array(
                    "status" => true,
                    "message" => "Person deleted successfully"
                ));
            } else {
                throw new Exception("Person with that ID does not exist");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
