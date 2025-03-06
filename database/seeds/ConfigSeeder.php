<?php

use App\Models\Configurations;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $config = [
            [
                'key' => 'stripe_public_key',
                'value' => 'pk_test_51Qzj4ULZMclAngVvpwvlzN4IPtUp0uv7GxdwqOgDhBDK1uzWkiNDyn4DsGx4LGRCdrRjIWFaslbHWapi8RofbKXk00TnOm9SJC',

            ],
            [
                'key' => 'stripe_secret_key',
                'value' => 'sk_test_51Qzj4ULZMclAngVvRPhXO01rOhqMcpH7xVHtV5GzH7FvLqMqQAdnTF0J665cKVJNh3SXb02QQsFyjjuYT4Vuesch00322d6vra',
            ],
        ];

        foreach ($config as $key => $value) {
            Configurations::create($value);

        }

    }
}
