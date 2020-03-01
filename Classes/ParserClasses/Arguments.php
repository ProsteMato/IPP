<?php
/**
 * @file    Arguments.php
 * @class   Arguments
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class will store data about arguments
 */

class Arguments
{
    private string $type;
    private string $content;

    public function __construct(string $type, string $content)
    {
        $this->type = $type;
        $this->content = $content;
    }

    /**
     * @brief get type of argument
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @brief get content of argument
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
