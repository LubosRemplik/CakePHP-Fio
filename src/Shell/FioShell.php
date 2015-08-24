<?php
namespace Lubos\Fio\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Network\Http\Client;

class FioShell extends Shell
{

    /**
     * Constructs this Shell instance.
     *
     * @param \Cake\Console\ConsoleIo $io An io instance.
     * @link http://book.cakephp.org/3.0/en/console-and-shells.html#Shell
     */
    public function __construct(ConsoleIo $io = null)
    {
        parent::__construct($io);
        $data = Configure::read('Fio');
        if (!isset($data['token'])) {
            $this->error('Please set up Fio token');
        }
        $this->client = new Client();
        $this->token = $data['token'];
        $this->url = 'https://www.fio.cz/ib_api/rest';
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('transactions', [
            'help' => 'Checking if domain is available',
            'parser' => [
                'options' => [
                    'from' => [
                        'help' => 'Date from in YYYY-MM-DD format'
                    ],
                    'to' => [
                        'help' => 'Date to in YYYY-MM-DD format'
                    ],
                ]
            ]
        ]);
        return $parser;
    }

    /**
     * Main function Prints out the list of shells.
     *
     * @return void
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
    }

    /**
     * transactions
     *
     * Returns fio transactions for period.
     *
     * options
     * - from Date from in YYYY-MM-DD format
     * - to Date to in YYYY-MM-DD format
     *
     * @return bool
     */
    public function transactions()
    {
        unset($this->params['help']);
        unset($this->params['verbose']);
        unset($this->params['quiet']);
        if (!empty($this->params)) {
            $data = array_merge($data, $this->params);
        }
        if (!isset($data['from'])) {
            $data['from'] = date('Y-m-d', strtotime('-1 month'));
        }
        if (!isset($data['to'])) {
            $data['to'] = date('Y-m-d');
        }
        $url = sprintf(
            '%s/periods/%s/%s/%s/transactions.json',
            $this->url,
            $this->token,
            $data['from'],
            $data['to']
        );
        $response = $this->client->get($url);
        if ($response->isOk()) {
            debug($response);
        } else {
            debug($response);
        }
        return false;
    }
}
