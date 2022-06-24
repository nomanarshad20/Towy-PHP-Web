<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\SocketController;
use App\Models\User;
use Illuminate\Console\Command;
use PHPSocketIO\SocketIO;
use Workerman\Worker;

class Socket extends Command
{
    public $socketService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SocketController $socketController)
    {
        parent::__construct();
        $this->socketService = $socketController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $io = new SocketIO(8081);
        $io->on('connection', function ($socket) use ($io) {
            $socket->emit('connected', [
                'result' => "success",
                'message' => 'Socket Connected Successfully',
                "data" => ['socket_id' => $socket->id]
            ]);

            $socket->on('updateSocketId', function ($data) use ($io, $socket) {
                return $this->socketService->updateUser($data, $socket, $io);
            });

            //driver will hit that and response will be send to passenger in case of active booking
            $socket->on('point-to-point-tacking', function ($data) use ($io, $socket) {
                return $this->socketService->p2pTracking($data,$io,$socket);
            });

            //driver will hit that and response will be send to passenger in case of active booking
            $socket->on('accept-reject-ride', function ($data) use ($io, $socket) {
                return $this->socketService->acceptRejectRide($data,$io,$socket);
            });

        });

        Worker::runAll();

    }
}
