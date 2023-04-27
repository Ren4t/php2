<?php

namespace Habr\Renat\blog;

use Habr\Renat\person\Person;

class Post {

    private int $id;
    private Person $author;
    private string $title;
    private string $text;

    public function __construct(int $id, Person $author, string $title, string $text) {
        $this->id = $id;
        $this->author = $author;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString() {
        return "Статья\n$this->author\n $this->title\n$this->text" . PHP_EOL;
    }

}
