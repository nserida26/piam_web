<div class="form-group <?php echo e($errors->has('code') ? 'has-error' : ''); ?>">
    <label for="code" class="control-label"><?php echo app('translator')->get('crud.code'); ?></label>
    <input class="form-control" name="code" type="text" id="code"
        value="<?php echo e(isset($moughatum->code) ? $moughatum->code : ''); ?>">
    <?php echo $errors->first('code', '<p class="help-block">:message</p>'); ?>

</div>
<div class="form-group <?php echo e($errors->has('intitule') ? 'has-error' : ''); ?>">
    <label for="intitule" class="control-label"><?php echo app('translator')->get('crud.intitule'); ?></label>
    <input class="form-control" name="intitule" type="text" id="intitule"
        value="<?php echo e(isset($moughatum->intitule) ? $moughatum->intitule : ''); ?>">
    <?php echo $errors->first('intitule', '<p class="help-block">:message</p>'); ?>

</div>
<div class="form-group <?php echo e($errors->has('intitule_fr') ? 'has-error' : ''); ?>">
    <label for="intitule_fr" class="control-label"><?php echo app('translator')->get('crud.intitule_fr'); ?></label>
    <input class="form-control" name="intitule_fr" type="text" id="intitule_fr"
        value="<?php echo e(isset($moughatum->intitule_fr) ? $moughatum->intitule_fr : ''); ?>">
    <?php echo $errors->first('intitule_fr', '<p class="help-block">:message</p>'); ?>

</div>
<div class="form-group <?php echo e($errors->has('wilaya_id') ? 'has-error' : ''); ?>">
    <select name="wilaya_id" id="wilaya" class="form-control wilaya" runat="server">
        <option value=""><?php echo app('translator')->get('crud.select_wilaya'); ?></option>
        <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($item->id); ?>"><?php echo e($item->intitule); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="<?php echo e($formMode === 'edit' ? __('crud.update') : __('crud.create')); ?>">
</div>
<?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/moughatas/form.blade.php ENDPATH**/ ?>