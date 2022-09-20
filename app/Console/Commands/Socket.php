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
//            $user = $socket->handshake['query']['user_id'];
//            $socket->emit('connected', [
//                'result' => "success",
//                'message' => 'Socket Connected Successfully',
//                "data" => ['socket_id' => $socket->id]
//            ]);
//
//            $socket->on('updateSocketId', function ($data) use ($io, $socket) {
//                return $this->socketService->updateUser($data, $socket, $io);
//            });

            //driver will hit that and response will be send to passenger in case of active booking
            $socket->on('point-to-point-tracking', function ($data) use ($io, $socket) {
                return $this->socketService->p2pTracking($data, $io, $socket);
            });

            //accept and reject booking. Notification will be send by and also response will be send from socket as well
            $socket->on('accept-reject-ride', function ($data) use ($io, $socket) {
                return $this->socketService->acceptRejectRide($data, $io, $socket);
            });


            //accept and reject booking. Notification will be send by and also response will be send from socket as well
            $socket->on('driver-change-booking-driver-status', function ($data) use ($io, $socket) {
                return $this->socketService->changeBookingDriverStatus($data, $io, $socket);
            });

            //passenger side trigger notification for wallet payment
            $socket->on('passenger-wallet-payment', function ($data) use ($io, $socket) {
                return $this->socketService->walletPayment($data, $io, $socket);
            });

            //get driver last location for ride
            $socket->on('driver-last-location',function($data) use($io,$socket){
                return $this->socketService->getDriverLastLocation($data,$io,$socket);
            });


//            $socket->on('disconnect', function ($data) use ($io, $socket) {
//
//                if (!isset($data['socket_id'])) {
//                    $socket->emit('driverStatus', [
//                        'result' => 'error',
//                        'message' => 'Socket ID is a Required Field',
//                        'data' => null
//                    ]);
//                }
//
//                if (!$data['user_id']) {
//                    $io->to($data['socket_id'])->emit('driverStatus', [
//                            'result' => 'error',
//                            'message' => 'User ID is a Required Field',
//                            'data' => null
//                        ]
//                    );
//                }
//
//            });


        });

        Worker::runAll();

    }
}
