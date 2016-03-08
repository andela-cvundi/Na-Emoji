<?php

namespace Vundi\NaEmoji\Controllers;

use Vundi\NaEmoji\Models\Emoji;
use PDOException;
use Vundi\Potato\Exceptions\NonExistentID;
use Vundi\Potato\Exceptions\IDShouldBeNumber;

class EmojiController
{
    /**
     * Get all emojis using ORM's getAll() method
     */
    public static function All()
    {
        return Emoji::findAll();
    }

    /**
     *
     * @param  int $id Emoji ID to search in the database
     * @return array Array with a message based on the status
     */
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

    /**
     * Create a new Emoji. Get the values from
     * what has been passed in the body when sending the request
     * @param  array $data   Associative array containing values passed in the body of
     * the request
     * @return object   an emoji object
     */
    public static function newEmoji($data)
    {
        $emoji = new Emoji();
        $emoji->name = $data['name'];
        $emoji->char = $data['char'];
        $emoji->keywords = $data['keywords'];
        $emoji->category = $data['category'];
        $emoji->created_by = $data['username'];
        $emoji->date_created = date("Y-m-d H:i:s");
        $emoji->date_updated = date("Y-m-d H:i:s");
        $emoji->save();

        return $emoji;
    }

    /**
     * Update emoji function
     * @param  int $id   The id of the emoji you want to update
     * @param  associative array $data containing the fields to update
     * @return array       With a message generated based on the status
     */
    public static function updateEmoji($id, $data)
    {
        try {
            $emoji = Emoji::find($id);
            $emoji->name = $data['name'];
            $emoji->char = $data['char'];
            $emoji->keywords = $data["keywords"];
            $emoji->category = $data['category'];
            $emoji->date_created = date("Y-m-d H:i:s");
            $emoji->date_updated = date("Y-m-d H:i:s");
            $emoji->update();

            return $message = [
                'success' => true
            ];

        } catch (NonExistentID $e) {
            return $message = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
