<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <div class="navbar" id="navbar">
        <div style="display: flex; align-items: center;">
            <h5 style="margin-right: 10px;">Abhijeet ChatApp</h5>
            <button class="toggle-btn" id="toggle-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('searchUser') }}" id="searchuserform" method="POST" style="margin-bottom: 10px;">
            @csrf
            <div class="search-container">
                <input type="search" name="searchusers" id="searchusers" placeholder="Search.." class="search-input">
            </div>
        </form>

        <div>
            <button id="create-group-toggle" style="background-color: transparent; border: none;">
                <i class="fas fa-th-large" style="color: white"></i>
            </button>
        </div>

        <div class="create-group-options" id="create-group-options"
            style="display: none; position: absolute; background: white; border: 1px solid #dee2e6; border-radius: 0.5rem; z-index: 1000;">
            <div class="navoption" style="padding: 10px; cursor: pointer; display:flex;flex-direction:column;gap:10px">
                <button type="button" class="btn btn-drak" data-toggle="modal" data-target="#exampleModalCenter">
                    <i class="fas fa-user" style="margin-right: 5px"></i>My Profile
                  </button>
                <button type="button" class="btn btn-drak" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-users" style="margin-right: 5px"></i>Create new group
                </button>


            </div>

        </div>
    </div>


    <div class="sidebar" id="sidebar">
        <div class="option" id="contacts-toggle">
            <i class="fas fa-user-friends"></i> Contacts <i class="fas fa-chevron-down" style="margin-left: 5px"></i>
        </div>
        <div class="contacts" id="contacts" style="display: none;">
            @if (empty($users))
                <p>No Contacts to show</p>
            @else
                @foreach ($users as $user)
                    <a class="contact" href="#" data-receiver-id="{{ $user['id'] }}"
                        data-username="{{ $user['username'] }}"
                        style="text-decoration:none; display: flex; align-items: center;">
                        <img src="{{ asset('storage/' . $user['profile']) }}"
                            alt="{{ $user['username'] }}'s profile picture" class="profile-pic">
                        {{ $user['username'] }}
                    </a>
                   
                @endforeach
            @endif
        </div>



        <div class="option" id="requests-toggle">
            <i class="fas fa-user-plus"></i>
            Friend Requests
            @if (!empty($totalRequests) && count($totalRequests) > 0)
                <span class="badge badge-danger" style="margin-left: 5px;">{{ count($totalRequests) }}</span>
            @endif
            <i class="fas fa-chevron-down" style="margin-left: 5px"></i>
        </div>

        <div class="requests" id="friend-requests" style="display: none;">
            @if (empty($totalRequests))
                <h6>{{ $noRequestsMessage }}</h6>
            @else
                @foreach ($totalRequests as $request)
                    <div class="friend-request" id="request-{{ $request->id }}">
                        <img src="{{ asset('storage/' . $request->profile) }}"
                            alt="{{ $request->username }}'s profile picture" class="friendreqprofile-pic">
                        <span>{{ $request->username }}</span>
                        <button class="accept-request" data-request-id="{{ $request->id }}">Accept</button>
                        <button class="decline-request" data-request-id="{{ $request->id }}">Decline</button>
                    </div>
                @endforeach
            @endif
        </div>


        <div class="option" id="groups-toggle">
            <i class="fas fa-users"></i> Groups <i class="fas fa-chevron-down" style="margin-left: 5px"></i>
        </div>

        <div class="groups" id="groups" style="display: none;">
            @if ($groups->isEmpty())
                <p>No Groups to show</p>
            @else
                @foreach ($groups as $group)
                    <a class="group" href="#" data-group-id="{{ $group->id }}"
                        style="text-decoration:none; color:white;display: flex; align-items: center;">
                        <img src="{{ asset('storage/' . $group->profile) }}"
                            alt="{{ $group->name }}'s profile picture" class="groupprofile-pic" style="width: 60px;height:60px;border-radius:50%;">
                        <span style="margin-left: 5px">{{ $group->name }}</span>
                    </a>
                @endforeach
            @endif
        </div>

        <div class="option">
            <i class="fas fa-cog"></i> Settings
        </div>
        <div class="option">
            <i class="fas fa-info-circle"></i> About
        </div>

        <form action="{{ route('logout') }}" method="POST" style="margin-top: auto; padding: 10px 20px;">
            @csrf
            <button type="submit" class="logout-btn" id="logoutbtn">Logout</button>
        </form>

        @if (Auth::check())
            <p class="logged-in-user" style="margin-left: 25px; margin-top: 5px;">Logged in as:
                {{ Auth::user()->username }}</p>
        @endif
    </div>


    <div class="content" id="content">
        @yield('content')
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#toggle-btn').click(function() {
                $('#sidebar').toggleClass('hidden');
                $('#content').toggleClass('shifted');
                $(this).find('i').toggleClass('fa-times fa-bars');
                if ($('#sidebar').hasClass('hidden')) {
                    $(this).css('left', '15px');
                } else {
                    $(this).css('left', '220px');
                }
            });

            $('#create-group-toggle').click(function() {
                $('#create-group-options').slideToggle();
            });

            $(document).click(function(event) {
                if (!$(event.target).closest('#create-group-toggle').length && !$(event.target).closest(
                        '#create-group-options').length) {
                    $('#create-group-options').slideUp();
                }
            });
            $('#contacts-toggle').click(function() {
                $('#contacts').slideToggle();
                $('#friend-requests').slideUp();
            });

            $('#requests-toggle').click(function() {
                $('#friend-requests').slideToggle();
                $('#contacts').slideUp();
            });
            $('#groups-toggle').on('click', function() {
                $('#groups').slideToggle();
            });

            $('.accept-request').click(function() {
                const requestId = $(this).data('request-id');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('acceptFriendRequest') }}",
                    data: {
                        request_id: requestId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#notificationMessage').text(response.message);
                        $('#notificationModal').modal('show');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            $('.decline-request').click(function() {
                const requestId = $(this).data('request-id');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('rejectFriendRequest') }}",
                    data: {
                        request_id: requestId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            function updateStatus() {
                $('.contact').each(function() {
                    var contactId = $(this).data('receiver-id');
                    var statusElement = $('#status-' + contactId);

                    var isOnline = navigator.onLine; // Using navigator.onLine for demo purposes

                    if (isOnline) {
                        statusElement.text('● Online').css('color', 'lightgreen');
                    } else {
                        statusElement.text('● Offline').css('color', 'red');
                    }
                });
            }

            updateStatus();

            setInterval(updateStatus, 5000);

            // Event listeners for online/offline
            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);

        });
    </script>
</body>

</html>
