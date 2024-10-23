@extends('sidebar')

@section('title', 'Chat')

@section('content')

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="notificationMessage">
                    <!-- Message will be inserted here dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <!--create group Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Group</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('createGroup') }}" method="POST" id="groupform" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile">Profile Picture</label>
                                    <input type="file" class="form-control-file border p-2" name="profile"
                                        id="profile">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Group Name</label>
                                    <input type="text" class="form-control border-primary" name="name" id="name"
                                        placeholder="Enter group name">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control border-primary" name="description" id="description" rows="3"
                                placeholder="Enter group description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category">Add Members</label>
                            @if (empty($users))
                                <p>No Contacts to show</p>
                            @else
                                <div class="form-group">
                                    <div class="list-group border-primary">
                                        @foreach ($users as $user)
                                            <div class="list-group-item d-flex align-items-center justify-content-start">
                                                <input type="checkbox" name="members[]" value="{{ $user['id'] }}"
                                                    style="margin-right: 5px;">
                                                <img src="{{ asset('storage/' . $user['profile']) }}"
                                                    alt="{{ $user['username'] }}'s profile picture" class="profile-pic"
                                                    style="width: 30px; height: 30px; border-radius: 50%; margin-right: 8px;">
                                                <span>{{ $user['username'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                            @endif
                        </div>

                    </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-dark">Create</button>
            </div>
            </form>
        </div>
    </div>
    </div>

    <!-- My Profile Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: black;color:white">
                    <h5 class="modal-title" id="exampleModalLongTitle">My Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('updateprofile') }}" method="POST" enctype="multipart/form-data"
                        id="myprofileform">
                        @csrf
                        <div class="card p-4 shadow-sm">
                            <div class="text-center mb-4">
                                <img id="profilePreview" src="{{ asset('storage/' . Auth::user()->profile) }}"
                                    alt="Profile Picture" class="rounded-circle img-thumbnail" width="150px"
                                    height="150px">
                                <input type="file" name="profile" id="profileInput" class="form-control-file mt-3"
                                    onchange="previewProfileImage(event)">
                            </div>

                            <div class="form-group">
                                <label for="username" class="font-weight-bold">Username:</label>
                                <input type="text" name="username" class="form-control profile-input" id="username"
                                    value="{{ Auth::user()->username }}">
                            </div>

                            <div class="form-group">
                                <label for="email" class="font-weight-bold">Email:</label>
                                <input type="email" name="email" class="form-control profile-input" id="email"
                                    value="{{ Auth::user()->email }}">
                            </div>

                            <div class="text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-dark">Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="search-results" class="search-results-container" style="margin-top: 50px; display:none"></div>
    {{-- single message chat container --}}
    <div class="chat-container" style="display: none">
        <div class="chat-header d-flex align-items-center">
            <img src="" alt="" class="profile-pic-header">
            <h5 class="mb-0 ml-2"></h5>
            <p class="state" style="margin-top: 15px"></p>
        </div>
        <div class="chat-messages">
            <div id="chatContainer"></div> <!-- Change to a single chat container -->
            {{-- <div id="groupChatContainer"></div> <!-- for group chat --> --}}
        </div>
        <div class="message-input">
            <form id="messageform" action="{{ asset('sendMessage') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="hidden" value="{{ Auth::user()->id }}" name="sender_id" id="sender_id">
                    <input type="hidden" name="receiver_id" id="receiver_id">
                    <input type="text" class="form-control" name="message" placeholder="Type a message..."
                        aria-label="Type a message">
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="submit">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- group message container --}}
    <div class="group-chat-container" style="display: none">
        <div class="group-chat-header d-flex align-items-center">
            <img src="" alt="" class="group-profile-pic-header">
            <h5 class="mb-0 ml-2"></h5>
        </div>
        <div class="group-chat-messages">
            <div id="groupChatContainer"></div> <!-- for group chat -->
        </div>
        <div class="group-message-input">
            <form id="groupmessageform" action="{{ route('sendGroupMessage') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="hidden" value="{{ Auth::user()->id }}" name="sender_id" id="sender_id">
                    <input type="hidden" name="group_id" id="group_id" value="">
                    <input type="text" class="form-control" name="groupMessage" placeholder="Type a message..."
                        aria-label="Type a message">
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="submit">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="no-contact-message" class="card"
        style="display: block; margin: 50px auto; max-width: 500px; border: 1px solid #dee2e6; border-radius: 0.5rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <div class="card-body text-center">
            <h5 class="card-title">No Contacts Available</h5>
            <p class="card-text">Please select a contact to start messaging.</p>
            <a href="#" class="btn btn-primary" id="go-to-contacts">Go to Contacts</a>
        </div>
    </div>


    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            let isInteractionActive = false;
            $('#go-to-contacts').click(function(e) {
                e.preventDefault(); // Prevent the default anchor click behavior
                $('#contacts').slideDown(); // Slide down the contacts section
            });

            $('.contact').on('click', function(e) {
                $('#no-contact-message').hide();
                e.preventDefault();
                const receiverId = $(this).data('receiver-id');
                $('#receiver_id').val(receiverId);
                const username = $(this).data('username');
                const profilePic = $(this).find('img').attr('src');

                $('.chat-header h5').text(username);
                $('.chat-header img').attr('src', profilePic);
                $(".chat-header p").html(
                    '<span class="online-status" id="status-{{ $user->id }}" style="margin-left: 10px;">● {{ $user->is_online ? 'Online' : 'Offline' }}</span>'
                    );

                $(".chat-container").css('display', 'flex');


                fetchMessages(receiverId);
            });

            $('.group').on('click', function(e) {
                $('#no-contact-message').hide();
                e.preventDefault();

                const groupId = $(this).data('group-id');
                const groupName = $(this).find('span').text();
                const profilePic = $(this).find('img').attr('src');
                $("#group_id").val(groupId);

                $('.group-chat-header h5').text(groupName);
                $('.group-chat-header img').attr('src',
                    profilePic);

                $(".group-chat-container").css('display', 'flex');

                fetchGroupMessages(groupId);
            });

            // Handle two users message form submission
            $("#messageform").submit(function(e) {
                e.preventDefault();
                let form = $("#messageform")[0];
                let formdata = new FormData(form);
                $.ajax({
                    url: "{{ route('sendMessage') }}",
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('input[name="groupMessage"]').val('');
                        // $('#chatContainer').append(messageDiv);
                        // scrollToBottom();
                    },
                    error: function(err) {
                        console.log(err.responseText);
                    }
                });
            });

            //group message form submission 
            $("#groupmessageform").submit(function(e) {
                e.preventDefault();
                let form = $("#groupmessageform")[0];
                let formdata = new FormData(form);
                $.ajax({
                    url: "{{ route('sendGroupMessage') }}",
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(data) {
                        $('input[name="groupMessage"]').val('');
                        console.log(data.message);
                    },
                    error: function(err) {
                        console.log(err.responseText);
                    }
                });
            });

            // Toggle delete button on clicking the three dots
            $(document).on('click', '.dots', function() {
                const deleteBtn = $(this).next('.delete-message');
                deleteBtn.toggle();
                if (deleteBtn.is(':visible')) {
                    isInteractionActive = true; // Stop auto-refresh when interacting
                } else {
                    isInteractionActive = false; // Resume auto-refresh after closing the dots
                }
            });

            // Handle delete action
            $(document).on('click', '.delete-message', function(e) {
                e.preventDefault();
                let messageId = $(this).data('message-id');

                // Perform delete action here
                $.ajax({
                    url: `{{ url('deleteMessage') }}/${messageId}`, // Ensure correct URL
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}" // Include CSRF token in the request
                    },
                    success: function() {
                        $(e.target).closest('.message').remove(); // Remove message from DOM
                    },
                    error: function(err) {
                        let errorMessage = err.responseJSON ? err.responseJSON.message :
                            'An error occurred';
                        $('#notificationMessage').text(errorMessage);
                        $('#notificationModal').modal('show');
                    }
                });
            });

            function fetchMessages(receiverId) {
                if (!isInteractionActive) {
                    $.ajax({
                        url: "{{ asset('getMessages') }}/" + receiverId,
                        type: "GET",
                        success: function(data) {
                            $('#chatContainer').empty();
                            data.chats.forEach(function(chat) {
                                const messageDiv = `
                            <div class="message ${chat.sender_id == {{ Auth::user()->id }} ? 'sent' : 'received'}">
                                <p>${chat.message}</p>
                                <div class="options">
                                    <span class="dots"></span>
                                    <a href="#" class="delete-message" data-message-id="${chat.id}">Delete</a>
                                </div>
                            </div>`;
                                $('#chatContainer').append(messageDiv);
                            });
                            scrollToBottom();
                        },
                        error: function(err) {
                            console.log(err.responseText);
                        }
                    });
                }
            }

            setInterval(function() {
                var receiverId = $('#receiver_id').val();
                if (receiverId && !isInteractionActive) {
                    fetchMessages(receiverId);
                }

            }, 2000);




            function fetchGroupMessages(groupId) {
                if (!isInteractionActive) {
                    $.ajax({
                        url: "{{ asset('getGroupMessages') }}/" + groupId,
                        type: "GET",
                        success: function(data) {
                            console.log('Chats are:', JSON.stringify(data, null, 2));

                            $('#groupChatContainer').empty();
                            data.chats.forEach(function(chat) {
                                const messageDiv = `
                            <div class="groupmessage ${chat.sender_id == {{ Auth::user()->id }} ? 'sent' : 'received'}">
                                <p>${chat.message}</p>
                            </div>`;
                                $('#groupChatContainer').append(messageDiv);
                            });
                        },
                        error: function(err) {
                            console.log(err.responseText);
                        }
                    });
                }
            }
            setInterval(function() {
                var groupId = $('#group_id').val();
                if (groupId && !isInteractionActive) {
                    fetchGroupMessages(groupId);
                }
            }, 2000);


            function scrollToBottom() {
                $('.chat-messages').animate({
                    scrollTop: $('.chat-messages')[0].scrollHeight
                }, 500);
            }
        });

        // Search users
        $('#searchusers').on('keyup', function() {
            let searchValue = $(this).val();

            // If search value is empty, clear the search results
            if (searchValue.length === 0) {
                $('#search-results').css('display', 'none'); // Clear the search results
                $('#no-contact-message').show(); // Show the no contact message
                return; // Exit the function early
            }

            $('#search-results').css('display', 'block');
            $.ajax({
                url: '{{ route('searchUser') }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    searchusers: searchValue
                },
                success: function(data) {
                    $('#search-results').empty();
                    $('#no-contact-message').hide();
                    if (data.users.length > 0) {
                        $.each(data.users, function(index, user) {
                            $('#search-results').append(`
                                <div class="user-card" id="user-${user.id}">
                                    <img src="{{ asset('storage/') }}/${user.profile}" alt="${user.username}'s profile picture" class="profile-pic">
                                    <span class="username">${user.username}</span>
                                    <div class="button-group">
                                        <button class="view-profile-btn" data-user-id="${user.id}">View Profile</button>
                                        <button class="add-friend-btn" data-user-id="${user.id}">Add Friend</button>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        $('#search-results').append('<p>No users found.</p>');
                    }
                },
                error: function(err) {
                    console.log(err.responseText);
                }
            });
        });

        // Send friend request
        $(document).on('click', '.add-friend-btn', function() {
            const userId = $(this).data('user-id');
            const button = $(this);
            $.ajax({
                url: "{{ route('sendFriendRequest') }}",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    req_reciever: userId
                },
                success: function(response) {
                    $('#notificationMessage').text(response.message);
                    $('#notificationModal').modal('show');
                    console.log('Friend request sent:', response);
                    button.text('Request Sent').prop('disabled', true);
                },
                error: function(err) {
                    console.log('Error sending friend request:', err.responseText);
                }
            });


            //create group---->
            $("#groupform").submit(function(e) {
                e.preventDefault();
                let form = $("#groupform")[0];
                let formdata = new FormData(form);

                $.ajax({
                    url: "{{ route('createGroup') }}",
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('#notificationMessage').text(data.res);
                        $('#notificationModal').modal('show');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);

                    },
                    error: function(err) {
                        console.log(err.responseText);
                        const errorMessage = err.responseJSON ? err.responseJSON.message :
                            'An error occurred';
                        $('#notificationMessage').text(errorMessage);
                        $('#notificationModal').modal('show');
                    }
                });
            });


            // Preview Selected Profile Image 

            function previewProfileImage(event) {
                const reader = new FileReader();
                const imageField = document.getElementById('profilePreview');

                reader.onload = function() {
                    if (reader.readyState === 2) {
                        imageField.src = reader.result; // Set the preview image to the new file
                    }
                }

                reader.readAsDataURL(event.target.files[0]);
            }

            // edit profile---->
            $("#myprofileform").submit(function(e) {
                e.preventDefault(); // Prevent the default form submission
                let formData = new FormData(this); // Create FormData object from the form

                $.ajax({
                    url: "{{ route('updateprofile') }}", // Use the correct route
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json', // Expect a JSON response
                    success: function(data) {
                        $('#notificationMessage').text(data.message);
                        $('#notificationModal').modal('show'); // Show notification modal
                        setTimeout(function() {
                            location.reload(); // Reload the page after 1 second
                        }, 1000);
                    },
                    error: function(err) {
                        const errorMessage = err.responseJSON && err.responseJSON.message ?
                            err.responseJSON.message :
                            'An error occurred';
                        $('#notificationMessage').text(errorMessage);
                        $('#notificationModal').modal('show'); // Show notification modal
                        console.log('Error:', err.responseText); // Log error for debugging
                    }
                });
            });



        })
    </script>

@endsection
