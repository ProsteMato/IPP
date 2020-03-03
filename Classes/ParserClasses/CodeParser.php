<?php
/**
 * @file    CodeParser.php
 * @class   CodeParser
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class that is doing all the job. It starts witch checking src file and then generating it to the output.
 */

class CodeParser
{
    private Analysis $analysis;
    private Instruction $instruction;
    private FileManager $fileManager;
    private Stats $stats;
    private XmlGenerator $xmlGenerator;
    private array $labels;
    private int $order;

    public function __construct(Analysis $analysis, XmlGenerator $xmlGenerator, FileManager $fileManager, Instruction $instruction, Stats $stats)
    {
        $this->analysis = $analysis;
        $this->xmlGenerator = $xmlGenerator;
        $this->fileManager = $fileManager;
        $this->stats = $stats;
        $this->instruction = $instruction;
        $this->labels = array();
        $this->order = 1;
    }

    /**
     * @brief This method is parsing and checking line by line the input src file and generating the output xml representation
     * @throws InvalidHeaderException
     * @throws InvalidInstructionException
     */
    public function parse()
    {
        $token = $this->fileManager->getNextToken();
        $this->analysis->isHeader($token);
        $this->xmlGenerator->generateHeader();
        $token = $this->fileManager->getNextToken();
        while (!$this->analysis->isEndingToken($token)) {
            $arguments = $this->analysis->argParser($token);
            if (strcmp(mb_strtoupper($token[0]), "LABEL") == 0)
                $this->checkUniqLabel($arguments[0]->getContent());
            $this->instruction->setOpCode(strtoupper($token[0]));
            $this->instruction->setArguments($arguments);
            $this->xmlGenerator->generateInstruction($this->instruction, $this->order);
            $token = $this->fileManager->getNextToken();
            $this->order++;
            $this->stats->incLoc();
        }
        $this->xmlGenerator->generate();
    }

    private function checkUniqLabel(string $name) {
        if(!in_array($name, $this->labels)) {
            $this->stats->incLabels();
            array_push($this->labels, $name);
        }
    }
}
