<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class MailSettingDTO
{
    public function __construct(
        public readonly string $driver,
        public readonly string $host,
        public readonly int $port,
        public readonly string $from_address,
        public readonly string $from_name,
        public readonly string $username,
        public readonly string $password,
        public readonly string $encryption
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            driver: $request->input('driver'),
            host: $request->input('host'),
            port: (int) $request->input('port'),
            from_address: $request->input('from_address'),
            from_name: $request->input('from_name'),
            username: $request->input('username'),
            password: trim($request->input('password')),
            encryption: $request->input('encryption')
        );
    }
}
