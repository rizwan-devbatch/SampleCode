package com.architecture.android;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.List;
public class CouponAdapter extends ListArrayAdapter<Coupon> {
    private Context context;
    DisplayImageOptions options;
    private CouponFragment couponFragment;
    Coupon coupon;
    public CouponAdapter(Context context, int resource,
                         int textViewResourceId, List<Coupon> objects, CouponFragment frag) {
        super(context, resource, textViewResourceId, objects);
        this.context = context;
        items = objects;
        this.couponFragment = frag;
        options = new DisplayImageOptions.Builder()
                .showImageOnLoading(R.drawable.ic_stub)
                .showImageForEmptyUri(R.drawable.ic_empty)
                .showImageOnFail(R.drawable.ic_error)
                .cacheInMemory(true)
                .cacheOnDisk(true)
                .considerExifParams(true)
                .bitmapConfig(Bitmap.Config.RGB_565)
                .build();
        // TODO Auto-generated constructor stub
    }
    private static class ViewHolder {
        TextView textViewHeading;
        TextView textViewDescription;
        TextView textViewRedeem;
        ImageView imageViewCouponMainImage;
        TextView  textViewAdvertiserName,textViewMoreDetails;
    }
    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        View view;
        ViewHolder holder;
        ViewGroup viewGroup;
         coupon = getItem(position);
        if(convertView == null)
        {
            viewGroup = (ViewGroup) LayoutInflater.from(
                    context.getApplicationContext()).inflate(
                    R.layout.coupon_item, null);
            holder = new ViewHolder();
            holder.textViewHeading = (TextView) viewGroup.findViewById(R.id.textViewSavePrice);
            holder.textViewDescription = (TextView) viewGroup.findViewById(R.id.textViewSavePriceDetail);
            holder.textViewRedeem = (TextView) viewGroup.findViewById(R.id.textViewRedeem);
            holder.imageViewCouponMainImage = (ImageView) viewGroup.findViewById(R.id.imageViewCouponMainImage);
            holder.textViewAdvertiserName = (TextView) viewGroup.findViewById(R.id.textViewAdvertiserName);
            holder.textViewMoreDetails = (TextView) viewGroup.findViewById(R.id.moreDetails);
            view = viewGroup;
            view.setTag(holder);
            convertView = view;
        } else {
            holder = (ViewHolder) convertView.getTag();
            view = convertView;
        }
        holder.textViewRedeem.setTag(position);
        holder.textViewMoreDetails.setTag(position);
        ImageLoader.getInstance()
                .displayImage(coupon.getCouponImageUrl(), holder.imageViewCouponMainImage, options, new SimpleImageLoadingListener() {
                });
        holder.textViewHeading.setText("" + coupon.getCouponHeading());
        String description = coupon.getCouponDescription();
        holder.textViewRedeem.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                coupon = getItem(Integer.parseInt(v.getTag()+""));
                confirmRedeem();
            }
        });
        if(description.length() > 38 ) {
            description = description.substring(0, 17) + "..";
            holder.textViewMoreDetails.setVisibility(View.VISIBLE);
        }
        else
         holder.textViewMoreDetails.setVisibility(View.GONE);
        holder.textViewDescription.setText(description);
        holder.textViewMoreDetails.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                coupon = getItem(Integer.parseInt(v.getTag()+""));
                AlertDialog alertDialog = new AlertDialog.Builder(couponFragment.getParent()).create();
                alertDialog.setTitle(coupon.getCouponHeading());
                alertDialog.setCanceledOnTouchOutside(false);
                alertDialog.setMessage(coupon.getCouponDescription());
                alertDialog.setButton(AlertDialog.BUTTON_NEUTRAL, "OK",
                        new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int which) {
                                dialog.dismiss();
                            }
                        });
                alertDialog.show();
            }
        });
        String adver="";
        try {
             adver = coupon.getCampaign().getAdvetizer_name();

        } catch(Exception e){
               e.printStackTrace();
            }
        holder.textViewAdvertiserName.setText(adver);
        holder.textViewAdvertiserName.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                new getHeader(coupon.getCampaign()).execute();
            }
        });
        // TODO Auto-generated method stub
        return convertView;
    }
}