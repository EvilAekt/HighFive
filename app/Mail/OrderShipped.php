<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan HIGHFIVE Anda Telah Dikirim! (#' . $this->order->order_code . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.shipped',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order]);
        
        return [
            Attachment::fromData(fn () => $pdf->output(), 'Invoice-' . $this->order->order_code . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
