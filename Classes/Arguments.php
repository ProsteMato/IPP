<?php


class Arguments
{
    private string $type;
    private string $content;

    public function __construct(string $type, string $content)
    {
        $this->type = $type;
        $this->content = $content;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContent()
    {
        return $this->content;
    }
}
