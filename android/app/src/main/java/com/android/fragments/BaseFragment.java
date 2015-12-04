package com.android.fragments;
import android.app.Activity;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import com.architecture.android.MainActivity;
import com.android.interfaces.OnBackPressedGeneric;

public class BaseFragment extends Fragment implements OnBackPressedGeneric{
    private Activity activity;

    private MainActivity parent;
    private final String TAG = MainActivity.class.getName().toString();

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        // TODO Auto-generated method stub
        super.onActivityCreated(savedInstanceState);
    }

    @Override
    public void onAttach(Activity activity) {
        // TODO Auto-generated method stub
        super.onAttach(activity);
        this.activity = activity;
        setParent(getYQHomeActivity());
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        // TODO Auto-generated method stub
        super.onCreate(savedInstanceState);
        setRetainInstance(true);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // TODO Auto-generated method stub
        return super.onCreateView(inflater, container, savedInstanceState);
    }

    @Override
    public void onPause() {
        // TODO Auto-generated method stub
        super.onPause();
    }

    @Override
    public void onResume() {
        // TODO Auto-generated method stub
        super.onResume();
    }

    @Override
    public void onStart() {
        // TODO Auto-generated method stub
        super.onStart();
    }

    @Override
    public void onStop() {
        // TODO Auto-generated method stub
        super.onStop();
    }
    public MainActivity getYQHomeActivity() {
        try {
            return (MainActivity) activity;
        } catch (Exception ex) {
            return null;
        }
    }
    public MainActivity getParent() {
        return parent;
    }
    public void setParent(MainActivity parent) {
        this.parent = parent;
    }
    @Override
    public void onBackPressed() {
    }

}
