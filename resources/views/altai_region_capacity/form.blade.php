<div class="card">
    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">
                <label class="form-label">Огноо</label>
                <input type="date" name="date" class="form-control"
                    value="{{ old('date', $item->date ?? now()->toDateString()) }}" required>

            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Хамгийн их ачаалал (МВт)</label>
                <input type="number" step="0.001" name="max_load" class="form-control"
                    value="{{ old('max_load', $item->max_load ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Хамгийн бага ачаалал (МВт)</label>
                <input type="number" step="0.001" name="min_load" class="form-control"
                    value="{{ old('min_load', $item->min_load ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">ББЭХС-ээс авсан (МВт)</label>
                <input type="number" step="0.001" name="import_from_bbexs" class="form-control"
                    value="{{ old('import_from_bbexs', $item->import_from_bbexs ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">ТБНС-ээс авсан (МВт)</label>
                <input type="number" step="0.001" name="import_from_tbns" class="form-control"
                    value="{{ old('import_from_tbns', $item->import_from_tbns ?? '') }}">
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Тайлбар</label>
                <textarea name="remark" class="form-control" rows="3">{{ old('remark', $item->remark ?? '') }}</textarea>
            </div>

        </div>

    </div>
</div>
