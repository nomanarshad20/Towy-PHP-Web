<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class DriverPortalDataProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = 'DROP PROCEDURE IF EXISTS sp_NewDriverPortalData;

        CREATE PROCEDURE sp_NewDriverPortalData (IN driverId int,in fromDate date,in toDate date)

        BEGIN

        select

        ifnull((select sum(b.actual_fare) from bookings b
        where b.driver_id = u.id
            and date(b.created_at) between fromDate and toDate),0) as totalRideActualAmount,

        ifnull((select sum(bd.passenger_total_cash_paid) from bookings bk
        INNER JOIN booking_details bd
        ON bk.id = bd.booking_id
        where bk.driver_id = u.id
            and date(bk.created_at) between fromDate and toDate),0) as totalCashCollectedByDriver,


        ifnull((select sum(bdex.passenger_extra_cash_paid) from bookings bkex
        INNER JOIN booking_details bdex
        ON bkex.id = bdex.booking_id
        where bkex.driver_id = u.id
            and date(bkex.created_at) between fromDate and toDate),0) as  totalPassengerPaidExtraAmount,


        ifnull((select sum(bdpw.passenger_wallet_paid) from bookings bkpw
        INNER JOIN booking_details bdpw
        ON bkpw.id = bdpw.booking_id
        where bkpw.driver_id = u.id
            and date(bkpw.created_at) between fromDate and toDate),0) as  totalPassengerWalletPaid,


        ifnull((select sum(bddf.cancel_ride_driver_fine_amount) from bookings bkdf
        INNER JOIN booking_details bddf
        ON bkdf.id = bddf.booking_id
        where bkdf.driver_id = u.id
            and date(bkdf.created_at) between fromDate and toDate),0) as  totalDriverCancelPenalty,


        ifnull((select sum(bdpp.cancel_ride_passenger_fine_amount) from bookings bkpp
        INNER JOIN booking_details bdpp
        ON bkpp.id = bdpp.booking_id
        where bkpp.driver_id = u.id
            and date(bkpp.created_at) between fromDate and toDate),0) as  totalPassengerCancelPenalty,


        ifnull((select sum(dwbns.amount) from drivers_wallet dwbns
        where dwbns.driver_id = driverId AND dwbns.type = "credit" AND dwbns.payment_method = "bonus"
            And date(dwbns.created_at) between fromDate and toDate),0) as driverTotalBonus,

        ifnull((select sum(dwcr.amount) from drivers_wallet dwcr
        where dwcr.driver_id = driverId AND dwcr.type = "credit" AND dwcr.payment_method = "cash"
            And date(dwcr.created_at) between fromDate and toDate),0) as totalDriverCreditAmount,

        ifnull((select sum(dwdr.amount) from drivers_wallet dwdr
        where dwdr.driver_id = driverId AND dwdr.type = "debit" AND dwdr.payment_method = "cash"
            And date(dwdr.created_at) between fromDate and toDate),0) as totalDriverDebitAmount,


        ifnull((select sum(drwp.amount) from drivers_wallet drwp
        where drwp.driver_id = driverId AND drwp.payment_method = "wallet"
            And date(drwp.created_at) between fromDate and toDate),0) as totalDriverWalletAmount,


        ifnull((select sum(tx.tax_amount) from franchise_wallets tx
        where tx.driver_id = driverId
            And date(tx.created_at) between fromDate and toDate),0) as totalTaxAmount,

        ifnull((select sum(fwcp.total_amount) from franchise_wallets fwcp
        where fwcp.driver_id = driverId AND fwcp.type = "debit" AND fwcp.payment_method = "cash_paid"
            And date(fwcp.created_at) between fromDate and toDate),0) as amountPaidToDriver,

        ifnull((select sum(fwcr.total_amount) from franchise_wallets fwcr
        where fwcr.driver_id = driverId AND fwcr.type = "credit" AND fwcr.payment_method = "cash_received"
            And date(fwcr.created_at) between fromDate and toDate),0) as amountReceivedFromDriver,


        ifnull((select count(bcomp.id) from bookings bcomp
        where bcomp.driver_status = 4 AND bcomp.ride_status = 4 AND bcomp.driver_id = u.id
            and date(bcomp.created_at) between fromDate and toDate),0) as  totalCompletedRides,


        ifnull((select count(bpc.id) from bookings bpc
        where bpc.driver_status >= 0 AND bpc.ride_status = 2 AND bpc.driver_id = u.id
            and date(bpc.created_at) between fromDate and toDate),0) as  totalPassengerCancelRides,


        ifnull((select count(bdc.id) from bookings bdc
        where bdc.driver_status >= 0 AND bdc.ride_status = 5 AND bdc.driver_id = u.id
            and date(bdc.created_at) between fromDate and toDate),0) as  totalDriverCancelRides,


        ifnull((select count(bac.id) from bookings bac
        where bac.driver_status >= 0 AND bac.ride_status = 3 AND bac.driver_id = u.id
            and date(bac.created_at) between fromDate and toDate),0) as  totalSystemCancelRides,


        ifnull((select avg(bkrt.rating) from booking_ratings bkrt
        where  bkrt.receiver_id = u.id
            and date(bkrt.created_at) between fromDate and toDate),0) as  ratingsAvg,


        ifnull((select count(asbd.id) from assign_booking_drivers asbd
        where  asbd.driver_id = u.id
            and date(asbd.created_at) between fromDate and toDate),0) as  totalReceivedRides,


        ifnull((select count(asbda.status) from assign_booking_drivers asbda
        where asbda.status = 1 AND asbda.driver_id = u.id
            and date(asbda.created_at) between fromDate and toDate),0) as  totalAcceptRides,


        ifnull((select count(asbdr.status) from assign_booking_drivers asbdr
        where asbdr.status = 0 AND asbdr.driver_id = u.id
            and date(asbdr.created_at) between fromDate and toDate),0) as  totalRejectRides,


        ifnull((select count(asbdi.status) from assign_booking_drivers asbdi
        where asbdi.status = 2 AND asbdi.driver_id = u.id
            and date(asbdi.created_at) between fromDate and toDate),0) as  totalIgnoreRides



        from users u where u.id = driverId;

        END';

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('procedure__new_driver_portal_data');
        $sql = "DROP PROCEDURE IF EXISTS sp_NewDriverPortalData";
        DB::connection()->getPdo()->exec($sql);
    }
}
