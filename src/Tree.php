<?php

namespace Fgm\MeteorListGraph;

use Fgm\MeteorListGraph\Node as DepNode;
use Grafizzi\Graph\Attribute;
use Grafizzi\Graph\Edge;
use Grafizzi\Graph\Graph;
use Grafizzi\Graph\Node;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class Tree {

  /**
   * @var \Fgm\MeteorListGraph\Node[]
   */
  protected $deps;

  /**
   * @var \Pimple\Container
   */
  protected $dic;

  protected $sourceTree;

  public function __construct(
    array $deps,
    Container $dic
  ) {
    $this->deps = $deps;
    $this->dic = $dic;
  }

  public function grow() {
    $current = new DepNode('');
    $stack = [];
    $nodes = [];

    foreach ($this->deps as $depRow) {
      $currentDepth = $current->getDepth();

      $depNode = new DepNode($depRow);
      $depDepth = $depNode->getDepth();
      // If we are a child of the previous node.
      if ($depDepth > $currentDepth) {
        // Stack if already good.
      }
      // If we are a sibling of the previous node
      elseif ($depDepth === $currentDepth) {
        // Pop it from the stack to get its parent.
        array_shift($stack);
      }
      // Else we are a child of some ancestor. Adjust which one based on depths.
      else {
        array_splice($stack, 0, 1 + $currentDepth - $depDepth);
      }

      $parent = $stack[0] ?? null;
      $depNode->ensureParsed();
      $depNode->parent = $parent;

      // Push node to stack.
      array_unshift($stack, $depNode);
      $nodes[] = $current = $depNode;
    }

    $this->sourceTree = $nodes;
  }

  public function flower() {
    $dic = $this->dic;
    $g = new Graph($dic);
    $rankDir = new Attribute($dic, 'rankdir', 'LR');
    $g->setAttribute($rankDir);
    $g->setDirected(true);
    /** @var \Fgm\MeteorListGraph\Node $node */
    foreach ($this->sourceTree as $node) {
      $gNode = new Node($dic, $node->req);
      $g->addChild($gNode);
      if ($node->parent) {
        $gParent = new Node($dic, $node->parent->req);
        $name = substr(hash('crc32b', $gParent->getName()), 0, 6);
        $red = $name[0] . $name[1];
        $green = $name[2] . $name[3];
        $blue = $name[4] . $name[5];
        //print_r([$name, $red, $green, $blue]);
        $color = "#$red$green$blue)";
        $colorAttr = new Attribute($dic, 'color', $color);
        $edge = new Edge($dic, $gParent, $gNode, [$colorAttr]);
        $g->addChild($edge);
      }
    }
    $dot = $g->build();
    return $dot;
  }
}
