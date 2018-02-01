<?php

namespace Fgm\MeteorListGraph;

use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase {

  public function fixtureProvider() {
    $rows = file(__DIR__ . '/../fixtures/ranking.txt');
    $data = array_map(function (string $row) {
      return [$row];
    }, $rows);

    return $data;
  }

  /**
   * @dataProvider fixtureProvider
   */
  public function testGetDepth($raw) {
    $node = new Node($raw);
    $len = $node->getDepth();
    // Just make sure we didn't trigger any exception and result is sane.
    $this->assertGreaterThanOrEqual(0, $len);
  }

  /**
   * @dataProvider fixtureProvider
   *
   * @param string $row
   */
  public function test__construct($row) {
    $node = new Node($row);

    $node->ensureParsed();
    $this->assertSame(false, strpos("\n", $node->req), "req does not contain a LF");
    $atPos = strpos($node->req, '@');
    $this->assertTrue($atPos > 0, "at sign found");
    // echo "$node\n";
  }

}
