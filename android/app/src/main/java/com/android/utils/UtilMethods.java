package com.android.utils;

import android.app.Activity;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Build;
import android.provider.DocumentsContract;
import android.provider.MediaStore;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

/**
 * Created by DevBatch on 11/2/2015.
 */
public class UtilMethods {

   
    public static String getPath(final Activity context, final Uri uri) {

        String url = uri.toString();

        if (url.startsWith("content://com.google.android.apps.photos.content")){
            String filePath = "";

            File myDir = new File(Constants.EXTERNAL_FILE_PHOTO_DIRECTORY);
            if (myDir.exists()) {
                myDir.delete();
            }
            myDir.mkdirs();

            File f = new File(Constants.EXTERNAL_FILE_PHOTO_DIRECTORY, Constants.EXTERNAL_FILE_PATH);

            if (f.exists())
                f.delete();

            Bitmap bitmap = null;
            InputStream is = null;
            try {
                is = context.getContentResolver().openInputStream(Uri.parse(url));

                OutputStream os = new FileOutputStream(f);
                bitmap = BitmapFactory.decodeStream(is);
                bitmap.compress(Bitmap.CompressFormat.JPEG, 100, os);
                is.close();
                os.close();
                filePath = f.getAbsolutePath();
            } catch (FileNotFoundException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
            }
            catch(Exception e ){

            }
            return filePath;

        }
        else{
            return UtilMethods.getPath(uri, context);
        }
    }

    /**
     * Get Uri and convert filePath to String
     *
     * @param uri
     * @param activity
     * @return
     */
    public static String getPath(Uri uri, Activity activity) {
        try {
            String[] projection = { MediaStore.MediaColumns.DATA };
            @SuppressWarnings("deprecation")
            Cursor cursor = activity.managedQuery(uri, projection, null, null, null);

            if (cursor!=null) {
                int column_index = cursor.getColumnIndexOrThrow(MediaStore.MediaColumns.DATA);
                cursor.moveToFirst();
                return cursor.getString(column_index);
            }
            else{
                return uri.getPath();
            }

        } catch (IllegalArgumentException e) {
            e.printStackTrace();
            return null;
        }
    }
    public static String getNewDevicesPath(final Activity context, Uri uri) {
        if( uri == null ) {
            return null;
        }
        String[] projection = { MediaStore.Images.Media.DATA };

        Cursor cursor;
        if(Build.VERSION.SDK_INT > 19)
        {
            String wholeID = DocumentsContract.getDocumentId(uri);
            String id = wholeID.split(":")[1];
            String sel = MediaStore.Images.Media._ID + "=?";

            cursor = context.getContentResolver().query(MediaStore.Images.Media.EXTERNAL_CONTENT_URI,
                    projection, sel, new String[]{ id }, null);
        }
        else
        {
            cursor = context.getContentResolver().query(uri, projection, null, null, null);
        }
        String path = null;
        try
        {
            if (cursor!=null) {
                int column_index = cursor
                        .getColumnIndex(MediaStore.Images.Media.DATA);
                cursor.moveToFirst();
                path = cursor.getString(column_index).toString();
                cursor.close();
            }
            else{
                uri.getPath();
            }
        }
        catch(NullPointerException e) {

        }
        return path;
    }
}
