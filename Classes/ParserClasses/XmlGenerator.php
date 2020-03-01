<?php
/**
 * @file    XmlGenerator.php
 * @class   XmlGenerator
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class is generating output xml file for IPPcode20 source file.
 */

class XmlGenerator
{
    private XMLWriter $xmlWriter;

    public function __construct()
    {
        $this->xmlWriter = new XMLWriter();
    }

    /**
     * @param Instruction $instruction  stored instruction to generate
     * @param int $order    order of instruction
     * @brief   This method will generate Instruction and store it into xmlWriter object
     */
    public function generateInstruction(Instruction $instruction, int $order)
    {
        $arguments = $instruction->getArguments();
        $this->xmlWriter->startElement("instruction");
        $this->xmlWriter->writeAttribute("order", $order);
        $this->xmlWriter->writeAttribute("opcode", $instruction->getOpCode());
        for($i = 1; $i <= count($arguments); $i++) {
            $this->xmlWriter->startElement("arg" . $i);
            $this->xmlWriter->writeAttribute("type", $arguments[$i - 1]->getType());
            $content = htmlspecialchars($arguments[$i - 1]->getContent(), ENT_XML1 | ENT_QUOTES, "UTF-8");
            $this->xmlWriter->writeRaw($content);
            $this->xmlWriter->endElement();
        }
        $this->xmlWriter->endElement();
    }

    /**
     * @brief This method generates the whole xml document into stdout
     */
    public function generate(){
        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();
        echo $this->xmlWriter->outputMemory(true);
    }

    /**
     * @brief This method generates Header of the xml document and stored it into the xmlWriter object
     */
    public function generateHeader()
    {
        $this->xmlWriter->openMemory();
        $this->xmlWriter->setIndent(true);
        $this->xmlWriter->setIndentString("    ");
        $this->xmlWriter->startDocument("1.0", "UTF-8");
        $this->xmlWriter->startElement("program");
        $this->xmlWriter->writeAttribute("language", "IPPcode20");
    }
}
