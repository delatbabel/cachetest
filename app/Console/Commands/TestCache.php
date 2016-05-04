<?php
/**
 * Class TestCache
 *
 * @author del
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Class TestCache
 *
 * Does some testing of the current Cache methods.
 */
class TestCache extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cache:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Cache';

    protected $iterations = 100;

    public function handle()
    {
        $this->output->writeln("Cache put/get test");
        $this->output->writeln("==================");
        for ($i=1; $i<$this->iterations; $i++) {
            $random_value = rand(100000, 9999999);

            Cache::put('test', $random_value, 60);

            $value_to_check = Cache::get('test', 'default');
            if ($value_to_check != $random_value) {
                $this->output->writeln("Iteration $i $random_value was stored but $value_to_check was retrieved");
            }
        }

        $this->output->writeln("Cache forget/remember/pull test");
        $this->output->writeln("===============================");
        for ($i=1; $i<$this->iterations; $i++) {
            $random_value = rand(100000, 9999999);

            Cache::forget('test');

            // This should return $random_value because the cache entry will not be present
            $value_to_check = Cache::remember('test', 60, function () use ($random_value) {
                return $random_value;
            });

            // This should return $random_value because the cache entry will be present and so
            // the closure will not be called
            $another_value = Cache::remember('test', 60, function () use ($random_value) {
                return $random_value + 1;
            });

            $value_to_check = Cache::pull('test');
            if ($value_to_check != $another_value) {
                $this->output->writeln("Iteration $i $random_value was stored but $another_value was retrieved");
            }
        }

        $this->output->writeln("Cache put/forget/get test");
        $this->output->writeln("=========================");
        for ($i=1; $i<$this->iterations; $i++) {
            $random_value = rand(100000, 9999999);

            Cache::put('test', $random_value, 60);
            Cache::forget('test');

            $value_to_check = Cache::get('test', 'default');
            if ($value_to_check == $random_value) {
                $this->output->writeln("Iteration $i $random_value was stored and forgotten but also retrieved");
            }
        }

        $this->output->writeln("Cache tag test");
        $this->output->writeln("==============");
        for ($i=1; $i<$this->iterations; $i++) {
            $random_value = rand(100000, 9999999);

            Cache::tags(['testone', 'testtwo'])->put('testthree', $random_value, 60);

            $value_to_check = Cache::tags(['testone', 'testtwo'])->get('testthree');
            if ($value_to_check != $random_value) {
                $this->output->writeln("Iteration $i $random_value was stored but $value_to_check was retrieved");
            }
        }

        $this->output->writeln("Cache tag forget test");
        $this->output->writeln("=====================");
        for ($i=1; $i<$this->iterations; $i++) {
            $random_value = rand(100000, 9999999);

            Cache::tags(['testone', 'testtwo'])->put('testthree', $random_value, 60);
            Cache::tags(['testone', 'testtwo'])->forget('testthree');

            $value_to_check = Cache::tags(['testone', 'testtwo'])->get('testthree');
            if ($value_to_check == $random_value) {
                $this->output->writeln("Iteration $i $random_value was stored and forgotten but also retrieved");
            }
        }

        $this->output->writeln("Cache tag flush test");
        $this->output->writeln("====================");
        for ($i=1; $i<$this->iterations; $i++) {
            $random_value = rand(100000, 9999999);

            Cache::tags(['testone', 'testtwo'])->put('testthree', $random_value, 60);
            Cache::tags(['testone', 'testtwo'])->flush();

            $value_to_check = Cache::tags(['testone', 'testtwo'])->get('testthree');
            if ($value_to_check == $random_value) {
                $this->output->writeln("Iteration $i $random_value was stored and flushed but also retrieved");
            }
        }

        $this->output->writeln("Test forgetting a key that does not exist");
        $this->output->writeln("=========================================");

        // Should be able to forget something without an exception
        Cache::forget('mugwump');

        $this->output->writeln("Test flushing");
        $this->output->writeln("=============");

        // Ditto for flush
        Cache::flush();
    }
}
