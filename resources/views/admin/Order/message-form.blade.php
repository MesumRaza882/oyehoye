
<div class="container-fluid">
        <!-- Message -->
        <div class="row">
            <div class="col-12 pb-0">
                <div class="card shadow-none border-0  pb-0">
                    <div class="card-body pb-0 px-0">
                        @if(count($order->message)>0)
                        @foreach($order->message as $message)
                        <div class="message_container">
                            <div class="d-flex justify-content-between justify-content-center">
                                <span class="badge bg-primary text-center p-1">Sent :
                                    <span class="fw-bold">{{$message->created_at->format('d')}}</span>
                                    <span class="mos">{{$message->created_at->format('M')}}</span>
                                    <span class="yr">{{$message->created_at->format('Y')}}</span>
                                </span>
                                <span class="{{ $message->read_at == 1 ? '' : 'd-none'}} badge bg-secondary text-center p-1">Read <i class="fa-brands fa-readme"></i>
                                </span>
                            </div>
                            <p>
                                {{$message->message}}
                            </p>
                        </div>
                        <hr>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>