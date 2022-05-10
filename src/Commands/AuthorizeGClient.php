<?php

namespace NCSU\GClient\Commands;

use Illuminate\Console\Command;
use NCSU\GClient\Client;
use NCSU\GClient\Exceptions\MissingCredentialsException;

class AuthorizeGClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gclient:authorize {--reauth}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run to perform Google API authorization';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->option('reauth')) {
            unlink(base_path(\Arr::get(config('google'), 'token_file')));
        }
        try {
            $client = new Client( config( 'gclient' ), 'auto' );
            //dd($client->getClient());
            $this->info('Credentials successfully authorized');
            try {
                /** @var \Google_Service_Directory $directory */
                $directory = $client->make( 'directory' );
                $primaryDomain = $directory->customers->get( 'my_customer');
                dump($primaryDomain);
            } catch(\Exception $e) {
                $this->error($e->getMessage());
                $this->error($e->getCode());
            }
        } catch(MissingCredentialsException $e) {
            $this->error('Please provide the necessary OAuth2.0 and/or Service account credentials to get started');
        } catch(\Exception $e) {
            $this->error($e->getMessage());
        }
        return;
    }
}
