<?= $this->extend('layout/marketplace_body') ?>

<?= $this->section('content') ?>
<?php $subview = $subview ?? 'index'; ?>
<?= $this->include("{$content}/{$subview}") ?>
<?= $this->endSection() ?>
