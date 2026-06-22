<?php

namespace Tests\Unit;

use App\Support\ClientIp;
use Illuminate\Http\Request;
use Tests\TestCase;

class ClientIpTest extends TestCase
{
    public function test_normalizes_ipv4_mapped_address(): void
    {
        $this->assertSame('192.168.1.10', ClientIp::normalize('::ffff:192.168.1.10'));
    }

    public function test_prefers_ipv4_from_forwarded_headers(): void
    {
        $request = Request::create('/', 'POST', server: [
            'REMOTE_ADDR' => '2a02:d247:8101:a33d:1:1:7aca:8eff',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.45, 10.0.0.1',
        ]);

        $this->assertSame('203.0.113.45', ClientIp::resolve($request));
    }

    public function test_returns_ipv4_remote_addr_when_available(): void
    {
        $request = Request::create('/', 'POST', server: [
            'REMOTE_ADDR' => '198.51.100.22',
        ]);

        $this->assertSame('198.51.100.22', ClientIp::resolve($request));
    }
}
