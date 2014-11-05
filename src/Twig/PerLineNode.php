<?php
namespace Hostnet\Component\AccessorGenerator\Twig;

/**
 * Node representation of a {% perline %}{% endperline %}
 * block. The node has 2 arguemnts and one list of interal
 * nodes.
 *
 * Arugments:
 *   prefix:  The first block of normal text before any twig
 *            inside of the perline block. Only there if
 *            available.
 *
 *   postifx: The last block of normal text after any twig
 *            inside of the perline block. Only there if
 *            available.
 * Nodes:
 *   lines:   A single node if there is only a text or a print
 *            node inside of the perline block, otherwise a
 *            Twig_Node with sub nodes of all the nodes between
 *            the prefix and/or postfix.
 *
 * @author Hidde Boomsma <hboomsma@hostnet.nl>
 */
class PerLineNode extends \Twig_Node
{
    /**
     * Create new PerLineNode
     *
     * @param \Twig_NodeInterface $lines
     * @param string              $prefix
     * @param string              $postfix
     * @param int                 $lineno
     * @param string              $tag
     */
    public function __construct(\Twig_NodeInterface $lines, $prefix, $postfix, $lineno, $tag = 'perline')
    {
        parent::__construct(
            ['lines' => $lines],
            ['prefix' => $prefix, 'postfix' => $postfix],
            $lineno,
            $tag
        );
    }

    /**
     * Perform code generation in case there is a prefix or prostfix (or both).
     *
     * This function is safe for indenting and does not generate empty lines.
     *
     * @param \Twig_Compiler $compiler
     */
    private function compileComplex(\Twig_Compiler $compiler)
    {
        $prefix  = $this->getAttribute('prefix');
        $postfix = $this->getAttribute('postfix');
        $lines   = $this->getNode('lines');

        // We use normal subcompilation, which uses echo so we buffer our output.
        $compiler->write("ob_start();\n");
        $compiler->subcompile($lines);

        $ltrim_prefix = ltrim($prefix);                        // Trimmed version for use on first line
        $indent       = ! trim($prefix) && ! trim($postfix);   // Are we only indenting or also prefixing

        // Fetch the content of the lines inside of this block
        // and itterate over them
        $compiler
            ->write("\$lines = explode(\"\\n\", ob_get_clean());\n")
            ->write("foreach (\$lines as \$key => \$line) {\n")
            ->indent(1);

        // If we are indenting code we have the risk of generating empty lines, so check for this.
        if ($indent) {
            $compiler
                ->write("if (trim(\$line)) {\n")
                ->indent(1);
        }

        // Write out the prefix for this line.
        if ($prefix) {
            $compiler->write("echo \$key > 0 ? '$prefix' : '$ltrim_prefix' ;\n");
        }

        // Write the line itself
        $compiler->write("echo \"\$line\";\n");

        // Close if statement for empty line check when indenting.
        if ($indent) {
            $compiler
                ->outdent(1)
                ->write("}\n");
        }

        // Write postfix and new line.
        $compiler
            ->write("echo \"$postfix\\n\";\n")
            ->outdent(1)
            ->write("}\n");
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        // Echo line information into the generated code
        $compiler->addDebugInfo($this);

        // If there are no prefix and postfix set, usage
        // of this tag is useless and we just display the
        // contents
        if ($this->getAttribute('prefix') || $this->getAttribute('postfix')) {
            $this->compileComplex($compiler);
        } else {
            $compiler->subcompile($this->getNode('lines'));
        }
    }
}