<?php

namespace Fgm\MeteorListGraph;


class Node {
  /* Glyphs used, left to right:
    - space
    - link to non-parent ancestor
    - link to parent for a last child
    - link to parent for a non-last child
    - spacer
    - non-root link to first child
   */
  protected const PREFIXES = '/^[ │└├─┬]*/';

  /**
   * Expressed in abstract depth units, not directly related with prefix length.
   *
   * @var int
   */
  protected $depth;

  /** @var string */
  public $note;

  /** @var \Fgm\MeteorListGraph\Node */
  public $parent;

  /** @var string */
  public $raw;

  /** @var string */
  public $req;

  public function getDepth() {
    if (!isset($this->depth)) {
      $rawDepth = preg_match(self::PREFIXES, $this->raw, $matches);
      assert($rawDepth, "Should always match at least an empty prefix");
      // Remember, we are using alphagraphics, outside the US-ASCII range, so
      // they take more than one byte, hence no strlen().
      $runeCount = mb_strlen($matches[0]);

      // Notice how root is irregularly drawn when compared with other levels.
      $this->depth = $runeCount ? ($runeCount - 2) / 2 : 0;
    }

    return $this->depth;
  }

  public function __construct(string $raw, ?Node $parent = null) {
    $this->raw = $raw;
    assert(isset($parent), $this->getDepth() === 0, "Only root node may be found at tree root.");
    $this->parent = $parent;
  }

  public function ensureParsed() {
    if (!isset($this->req)) {
      $depth = $this->getDepth();
      $raw = $depth ? mb_substr($this->raw, 2 + (2 * $depth)) : $this->raw;
      $exploded = explode(' ', $raw, 2);
      $this->req = trim(array_shift($exploded));
      $this->note = trim($exploded ? $exploded[0] : '', "()\n");
    }
  }

  public function __toString() {
    $this->ensureParsed();
    return sprintf("%3d %-40s %s", $this->getDepth(), $this->req, $this->note);
  }
}
