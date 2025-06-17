<?php
namespace ArtPulse\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use function Patchwork\replace;
use ArtPulse\Community\FavoritesRestController;

class FavoritesRestControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testToggleAddCallsManager()
    {
        $called = false;
        replace('ArtPulse\\Community\\FavoritesManager::add_favorite', function($uid, $oid, $otype) use (&$called) {
            $called = [$uid, $oid, $otype];
        });
        replace('ArtPulse\\Community\\FavoritesManager::remove_favorite', function(){});

        Functions\when('get_current_user_id')->justReturn(1);
        Functions\when('sanitize_text_field')->alias(fn($v) => $v);

        $res = FavoritesRestController::handle_toggle([
            'object_id'   => 5,
            'object_type' => 'event',
            'action'      => 'add'
        ]);

        $this->assertEquals(['success' => true, 'favorited' => true], $res);
        $this->assertSame([1,5,'event'], $called);
    }

    public function testToggleRemoveCallsManager()
    {
        $called = false;
        replace('ArtPulse\\Community\\FavoritesManager::add_favorite', function(){});
        replace('ArtPulse\\Community\\FavoritesManager::remove_favorite', function($uid,$oid,$otype) use (&$called){
            $called = [$uid,$oid,$otype];
        });

        Functions\when('get_current_user_id')->justReturn(2);
        Functions\when('sanitize_text_field')->alias(fn($v)=>$v);

        $res = FavoritesRestController::handle_toggle([
            'object_id'   => 7,
            'object_type' => 'artwork',
            'action'      => 'remove'
        ]);

        $this->assertEquals(['success' => true, 'favorited' => false], $res);
        $this->assertSame([2,7,'artwork'], $called);
    }

    public function testHandleGetReturnsFavorites()
    {
        replace('ArtPulse\\Community\\FavoritesManager::get_user_favorites', function(){
            return [ (object)[ 'object_id' => 9, 'object_type' => 'event', 'favorited_on' => '2024-01-01' ] ];
        });
        Functions\when('get_current_user_id')->justReturn(3);
        Functions\when('sanitize_text_field')->alias(fn($v)=>$v);
        Functions\when('get_post')->alias(fn($id) => (object)['post_type' => 'artpulse_event']);
        Functions\when('get_the_title')->justReturn('Test Event');
        Functions\when('get_permalink')->justReturn('http://example.test/event');
        Functions\when('get_the_post_thumbnail_url')->justReturn('http://img');
        Functions\when('rest_ensure_response')->alias(fn($v)=>$v);

        $res = FavoritesRestController::handle_get(['object_type' => 'event']);

        $this->assertIsArray($res);
        $this->assertCount(1, $res);
        $this->assertEquals('Test Event', $res[0]['title']);
    }
}
