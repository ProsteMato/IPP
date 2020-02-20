<?php


class XmlGenerator
{
    private XMLWriter $xmlWriter;

    public function __construct()
    {
        $this->xmlWriter = new XMLWriter();
    }

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

    public function generate(){
        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();
        echo $this->xmlWriter->outputMemory(true);
    }

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
