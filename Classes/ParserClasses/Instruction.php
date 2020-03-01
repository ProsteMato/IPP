<?php
/**
 * @file    Instruction.php
 * @class   Instruction
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class is for storing data of Instructions
 */

class Instruction
{
    private string $opCode;
    private array $arguments;

    public function __construct()
    {
        $this->opCode = "";
        $this->arguments = array();
    }

    /**
     * @return string   returns opCode of instruction
     */
    public function getOpCode()
    {
        return $this->opCode;
    }

    /**
     * @return array    return arguments of instruction
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @brief set the opCode of instruction
     * @param string $opCode    opCode of instruction
     */
    public function setOpCode(string $opCode)
    {
        $this->opCode = $opCode;
    }

    /**
     * @brief set the arguments of instruction
     * @param array $arguments    arguments of instruction
     */
    public function setArguments($arguments) {
        $this->arguments = $arguments;
    }
}
