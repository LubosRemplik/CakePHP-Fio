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
            'help' => 'Returns fio transactions for period.',
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
     * @return array
     */
    public function transactions()
    {
        $results = [];
        if (!isset($this->params['from'])) {
            $this->params['from'] = date('Y-m-d', strtotime('-1 month'));
        }
        if (!isset($this->params['to'])) {
            $this->params['to'] = date('Y-m-d');
        }
        $url = sprintf(
            '%s/periods/%s/%s/%s/transactions.json',
            $this->url,
            $this->token,
            $this->params['from'],
            $this->params['to']
        );
        $response = $this->client->get($url);
        if ($response->isOk()) {
            $body = json_decode($response->body);
            $transactions = $body
                ->accountStatement
                ->transactionList
                ->transaction;
            $num = 1;
            foreach ($transactions as $transaction) {
                $item = [];
                foreach ($transaction as $column) {
                    if (isset($column)) {
                        $item[$column->name] = $column->value;
                    }
                }
                if (!$this->params['quiet']) {
                    $out = sprintf(
                        '%03d. %s %s at %s',
                        $num++,
                        $item['Objem'],
                        $item['Měna'],
                        $item['Datum']
                    );
                    if (!empty($item['VS'])) {
                        $out .= sprintf(', VS: %s', $item['VS']);
                    }
                    if (!empty($item['Uživatelská identifikace'])) {
                        $out .= sprintf(', Message: %s', $item['Uživatelská identifikace']);
                    }
                    if ($item['Objem'] < 0) {
                        $out = sprintf('<warning>%s</warning>', $out);
                    } else {
                        $out = sprintf('<success>%s</success>', $out);
                    }
                    $this->out($out);
                }
                $results[] = $item;
            }
            if (!$this->params['quiet']) {
                $this->out(sprintf(
                    'Opening balance: %s',
                    $body->accountStatement
                        ->info
                        ->openingBalance
                ));
                $this->out(sprintf(
                    'Closing balance: %s',
                    $body->accountStatement
                        ->info
                        ->closingBalance
                ));
                $this->hr();
            }
        } else {
            debug($response);
        }
        return $results;
    }
}
