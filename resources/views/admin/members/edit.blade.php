@php
use \App\Models\User;
use \App\Models\UserType;
@endphp
@extends('admin.layout')

@section('page')
Edit Member {{ $user->username }}
@endsection

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>
  <li class="breadcrumb-item"><a href="{{ url('admin/members/list') }}">Members</a></li>
  <li class="breadcrumb-item active">Edit Member</li>
</ol>
@endsection

@section('content')
<div class="container">
  <form action="{{ url()->current() }}" method="POST">
    @csrf
    <div class="form-row mb-3">
      <label for="join_date" class="col-sm-2 col-form-label">Join Date</label>
      <div class="col-sm-5">
        <input type="date" name="join_date" value="{{ $user->join_date }}" class="form-control @error('join_date') border border-danger @enderror">
        @error('join_date')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <h4>Personal Informations</h4>
    <div class="form-row mb-3">
      <label for="name" class="col-sm-2 col-form-label">Name</label>
      <div class="col-sm-5">
        <input type="text" name="name" value="{{ $user->name }}" class="form-control @error('name') border border-danger @enderror">
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div class="form-row mb-3">
      <label for="surname" class="col-sm-2 col-form-label">Surname</label>
      <div class="col-sm-5">
        <input type="text" name="surname" value="{{ $user->surname }}" class="form-control @error('surname') border border-danger @enderror">
        @error('surname')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div class="form-row mb-3">
      <label for="username" class="col-sm-2 col-form-label">Username</label>
      <div class="col-sm-5">
        <input type="text" name="username" value="{{ $user->username }}" class="form-control @error('username') border border-danger @enderror">
        @error('username')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div class="form-row mb-3">
      <label for="email" class="col-sm-2 col-form-label">Email Address</label>
      <div class="col-sm-5">
        <input type="email" name="email" value="{{ $user->email }}" class="form-control @error('email') border border-danger @enderror">
        @error('email')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div class="form-row mb-3">
      <label for="country" class="col-sm-2 col-form-label">Country</label>
      <div class="col-sm-5">
        <select name="country" id="country" class="custom-select">
          @foreach ($countries as $country)
          <option value="{{ $country->code }}" {{ $user->country == $country->code ? 'selected' : ''}}>{{ $country->name }}</option>
          @endforeach
        </select>
        @error('country')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <h4>Email Preferences</h4>
    <div class="custom-control custom-checkbox">
      <input class="custom-control-input" type="checkbox" id="referral_notification" name="referral_notification" {{ $user->referral_notification ? "checked" : ""}}>
      <label for="referral_notification" class="custom-control-label font-weight-normal">Referral Notification</label>
    </div>
    <div class="custom-control custom-checkbox">
      <input class="custom-control-input" type="checkbox" id="commission_notification" name="commission_notification" {{ $user->commission_notification ? "checked" : ""}}>
      <label for="commission_notification" class="custom-control-label font-weight-normal">Commission Notification</label>
    </div>
    <div class="custom-control custom-checkbox">
      <input class="custom-control-input" type="checkbox" id="pm_notification" name="pm_notification" {{ $user->pm_notification ? "checked" : ""}}>
      <label for="pm_notification" class="custom-control-label font-weight-normal">Private Message Notification</label>
    </div>
    <h4 class="mt-3">Change Password</h4>
    <div class="form-row mb-3">
      <label for="password" class="col-sm-2 col-form-label">New Password</small></label>
      <div class="col-sm-5">
        <input type="password" name="password" class="form-control @error('password') border border-danger @enderror">
        @error('password')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <h4>Others</h4>
    <div class="form-row mb-3">
      <label for="upline" class="col-sm-2 col-form-label">Upline</label>
      <div class="col-sm-5">
        <input type="text" name="upline" value="{{ User::where('id', $user->upline)->value('username') }}" class="form-control @error('upline') border border-danger @enderror">
        @error('upline')
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div class="form-row mb-3">
      <label for="status" class="col-sm-2 col-form-label">Status</label>
      <div class="col-sm-5">
        <select name="status" id="status" class="custom-select">
          <option value="Unverified" {{ $user->status == 'Unverified' ? 'selected' : ''}}>Unverified</option>
          <option value="Active" {{ $user->status == 'Active' ? 'selected' : ''}}>Active</option>
          <option value="Suspended" {{ $user->status == 'Suspended' ? 'selected' : ''}}>Suspended</option>
        </select>
      </div>
    </div>
    <div id="suspend_options" class="d-none">
      <div class="form-row mb-3">
        <label for="status" class="col-sm-2 col-form-label">Suspend Reason</label>
        <div class="col-sm-5">
          <input type="text" name="suspend_reason" value="{{ $user->suspend_reason }}" class="form-control @error('suspend_reason') border border-danger @enderror">
          @error('suspend_reason')
          <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="form-row mb-3">
        <label for="status" class="col-sm-2 col-form-label">Suspend Until</label>
        <div class="col-sm-5">
          <input type="date" name="suspend_until" value="{{ $user->suspend_until }}" class="form-control @error('suspend_until') border border-danger @enderror">
          @error('suspend_until')
          <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>
    <div class="form-row mb-3">
      <label for="user_type" class="col-sm-2 col-form-label">Type</label>
      <div class="col-sm-5">
        <select name="user_type" id="user_type" class="custom-select">
          @foreach($user_types as $user_type)
          <option value="{{ $user_type->id }}" {{ $user->user_type == $user_type->id ? 'selected' : ''}}>{{ $user_type->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
    <a href="{{ url('admin/members/list') }}" class="btn btn-secondary mt-3">Cancel</a>
  </form>
</div>
@endsection

@section('scripts')
<script>
  $(function() {
    function toggleSuspendOptions() {
      if ($("#status option:selected").val() == "Suspended") {
        $("#suspend_options").removeClass("d-none");
      } else {
        $("#suspend_options").addClass("d-none");
      }
    }
    toggleSuspendOptions();
    $("#status").change(function(e) {
      toggleSuspendOptions();
    });
  });
</script>
@endsection