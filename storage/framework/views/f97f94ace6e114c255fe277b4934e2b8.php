<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

<title>
Documents Ready for Collection
<?php if(isset($appointment)): ?>
 - <?php echo e($appointment->appointment_no); ?>

<?php endif; ?>
</title>


</head>


<body>

<p>Dear <?php echo e($appointment->applicant_name); ?>,</p>

<p>
    We are pleased to inform you that your documents are ready for collection
    at the <strong>Embassy of India, Berlin</strong>.
</p>

<hr>

<p>
    <strong>Passport Number:</strong>
    <?php echo e($appointment->documentCollection->passport_no); ?>

    
    <span class="ms-4">
        <strong>Reference No:</strong>
        <?php echo e($appointment->ReferenceNr); ?>

    </span>
</p>

<p>
    <strong>Collection Date:</strong>
    <?php echo e(\Carbon\Carbon::parse($appointment->documentCollection->collection_date)->format('d M Y')); ?>

</p>

<p>
    <strong>Collection Time:</strong>
    <?php echo e($appointment->documentCollection->collection_time); ?>

</p>

<hr>

<p>
   Please ensure to bring a  copy of this email, while coming for collection of the documents at the Embassy. 
   
</p>
<p>
For OCI: In case if you have applied for Miscellaneous services or PIO to OCI  and not submitted your previous document then kindly bring your previous OCI card /PIO Card and submit it for cancellation. 
</p>
<p>
For Passport: In case the old passport is not cancelled, please bring it for cancellation
</p>
<p>
    In case you are not able to collect the documents personally, it is necessary to have a power of attonery 
    who should also present the personal details while collecting the documents
</p>
<p>
    <strong>Please do not reply to this email.</strong>
</p>


<p>
    Regards,<br>
    <strong>Embassy of India, Berlin</strong>
</p>

</body>
</html>
<?php /**PATH D:\Berlin-Appointments-25\resources\views/emails/document-collection-ready.blade.php ENDPATH**/ ?>