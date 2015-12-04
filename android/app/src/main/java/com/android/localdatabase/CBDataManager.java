package com.architecture.android.localdatabase;

import android.content.Context;
import java.util.List;

public class CBDataManager {

	private Context context;
	private static CBDataManager theInstance = null;
	private CBDataBaseManager databaseManager;

	@SuppressWarnings("unused")
	private static final String TAG = CBDataManager.class.getSimpleName()
			.toString();

	public CBDataManager(Context ctx, boolean recreate) {
		this.context = ctx;
		if (this.databaseManager == null) {
			this.databaseManager = new CBDataBaseManager(
					this.context.getApplicationContext(), recreate);
		}
	}

	public CBDataManager(Context context) {
		this.context = context;
		if (this.databaseManager == null) {
			this.databaseManager = new CBDataBaseManager(
					this.context.getApplicationContext(), false);
		}

	}

	public static CBDataManager getToastDataManager(Context context) {
		if (theInstance == null) {
			theInstance = new CBDataManager(context);
		}
		return theInstance;
	}

	public static CBDataManager reinitializeORMLiteDataManager(Context ctx) {
		theInstance = new CBDataManager(ctx, true);
		return theInstance;
	}

	public static CBDataBaseManager getDatabaseManager(Context context) {
		return getToastDataManager(context).databaseManager;
	}



	public CBDataBaseManager getDatabaseManager() {
		return databaseManager;

	}

}
