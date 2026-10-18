import React, { useState } from 'react';
import { useApp } from '../../context/AppContext';
import { DrugBatch } from '../../types';
import { UploadCloud, FileText, CheckCircle2, AlertCircle, X, Download, RefreshCw } from 'lucide-react';

interface CsvImportModalProps {
  isOpen: boolean;
  onClose: () => void;
}

interface ParsedRow {
  rowNum: number;
  drug_id: string;
  drug_name: string;
  batch_no: string;
  expiry_date: string;
  stock: number;
  burn_rate: number;
  status: 'valid_new' | 'valid_update' | 'error';
  errorMessage?: string;
}

export const CsvImportModal: React.FC<CsvImportModalProps> = ({ isOpen, onClose }) => {
  const { currentFacility, drugs, batches, importBatches } = useApp();
  const [csvText, setCsvText] = useState<string>('');
  const [parsedRows, setParsedRows] = useState<ParsedRow[]>([]);
  const [hasValidated, setHasValidated] = useState<boolean>(false);
  const [isSuccess, setIsSuccess] = useState<boolean>(false);

  if (!isOpen) return null;

  const sampleCsvData = `Drug_Code,Batch_No,Expiry_Date,Stock_Quantity,Daily_Burn_Rate
drug_amx500,AMX-2025-99B,2026-11-20,350,4.5
drug_pct_syr,PCT-2025-88S,2026-12-15,120,3.2
drug_ors,ORS-2025-990,2026-11-10,200,2.0
drug_met500,MET-2025-11X,2027-04-01,180,1.8
drug_aml10,AML-2025-45C,2027-02-28,95,1.2`;

  const handleDownloadTemplate = () => {
    const blob = new Blob([sampleCsvData], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `medicine_stock_template_${currentFacility.id}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const handleLoadSample = () => {
    setCsvText(sampleCsvData);
    handleParseAndValidate(sampleCsvData);
  };

  const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (event) => {
      const content = event.target?.result as string;
      setCsvText(content);
      handleParseAndValidate(content);
    };
    reader.readAsText(file);
  };

  const handleParseAndValidate = (rawText: string) => {
    const lines = rawText.trim().split(/\r?\n/);
    if (lines.length <= 1) {
      setParsedRows([]);
      setHasValidated(true);
      return;
    }

    const rows: ParsedRow[] = [];
    // Skip header line
    for (let i = 1; i < lines.length; i++) {
      const line = lines[i].trim();
      if (!line) continue;

      const cols = line.split(',').map((c) => c.trim().replace(/^"|"$/g, ''));
      const [drugId, batchNo, expiryDate, stockStr, burnRateStr] = cols;

      const drug = drugs.find((d) => d.id === drugId);
      const stock = parseInt(stockStr, 10);
      const burnRate = parseFloat(burnRateStr);

      let status: 'valid_new' | 'valid_update' | 'error' = 'valid_new';
      let errorMessage = '';

      if (!drugId || !drug) {
        status = 'error';
        errorMessage = `Drug ID "${drugId}" not found in national medicine catalog.`;
      } else if (!batchNo || batchNo.length < 3) {
        status = 'error';
        errorMessage = 'Invalid batch number format.';
      } else if (!expiryDate || isNaN(Date.parse(expiryDate))) {
        status = 'error';
        errorMessage = 'Invalid date format (must be YYYY-MM-DD).';
      } else if (isNaN(stock) || stock < 0) {
        status = 'error';
        errorMessage = 'Stock quantity must be a positive integer.';
      } else {
        // Check if existing
        const exists = batches.some(
          (b) => b.facility_id === currentFacility.id && b.batch_no === batchNo
        );
        status = exists ? 'valid_update' : 'valid_new';
      }

      rows.push({
        rowNum: i + 1,
        drug_id: drugId,
        drug_name: drug ? drug.name : drugId,
        batch_no: batchNo,
        expiry_date: expiryDate,
        stock: isNaN(stock) ? 0 : stock,
        burn_rate: isNaN(burnRate) ? 2.0 : burnRate,
        status,
        errorMessage,
      });
    }

    setParsedRows(rows);
    setHasValidated(true);
  };

  const handleConfirmImport = () => {
    const validRows = parsedRows.filter((r) => r.status !== 'error');
    if (validRows.length === 0) return;

    const batchesToImport: DrugBatch[] = validRows.map((r) => ({
      id: `batch_csv_${Date.now()}_${r.batch_no}`,
      facility_id: currentFacility.id,
      drug_id: r.drug_id,
      batch_no: r.batch_no,
      expiry_date: r.expiry_date,
      stock: r.stock,
      updated_at: new Date().toISOString(),
      source_supplier: 'Self-Service CSV Import',
    }));

    importBatches(batchesToImport);
    setIsSuccess(true);
    setTimeout(() => {
      onClose();
      setIsSuccess(false);
      setParsedRows([]);
      setHasValidated(false);
      setCsvText('');
    }, 1400);
  };

  const newCount = parsedRows.filter((r) => r.status === 'valid_new').length;
  const updateCount = parsedRows.filter((r) => r.status === 'valid_update').length;
  const errorCount = parsedRows.filter((r) => r.status === 'error').length;

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 font-body">
      <div className="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-[#DBE5F5] overflow-hidden">
        {/* Modal Header */}
        <div className="px-6 py-4 bg-[#F8FAFC] border-b border-[#DBE5F5] flex items-center justify-between">
          <div>
            <h3 className="text-base font-bold text-[#0B1F4B] flex items-center gap-2">
              <UploadCloud className="w-5 h-5 text-[#1D4ED8]" />
              <span>Import Medicine Stock via CSV</span>
            </h3>
            <p className="text-xs text-[#52627F] mt-0.5">
              Target Facility: <span className="font-semibold text-[#1D4ED8]">{currentFacility.name}</span>
            </p>
          </div>
          <button
            onClick={onClose}
            className="p-2 rounded-lg text-[#52627F] hover:text-[#0B1F4B] hover:bg-slate-200/60 transition-colors cursor-pointer"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-6 overflow-y-auto space-y-5 flex-1 text-sm text-[#0B1F4B]">
          {/* Action bar: Template download & quick demo sample button */}
          <div className="flex flex-wrap items-center justify-between gap-3 p-4 bg-[#EAF1FF] border border-[#DBE5F5] rounded-xl">
            <div className="text-xs">
              <p className="font-bold text-[#0B1F4B]">Use standardized health authority CSV format</p>
              <p className="text-[#52627F] text-[11px] mt-0.5 font-mono">
                Columns: Drug_Code, Batch_No, Expiry_Date, Stock_Quantity, Daily_Burn_Rate
              </p>
            </div>
            <div className="flex items-center gap-2">
              <button
                onClick={handleDownloadTemplate}
                className="px-3 py-1.5 text-xs font-semibold bg-white border border-[#DBE5F5] text-[#1D4ED8] rounded-lg hover:bg-[#F8FAFC] flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer"
              >
                <Download className="w-3.5 h-3.5" />
                <span>Download Template</span>
              </button>
              <button
                onClick={handleLoadSample}
                className="px-3 py-1.5 text-xs font-semibold bg-[#1D4ED8] text-white rounded-lg hover:bg-blue-800 flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer"
              >
                <RefreshCw className="w-3.5 h-3.5" />
                <span>Load Demo Dataset</span>
              </button>
            </div>
          </div>

          {/* Upload Dropzone */}
          <div className="border-2 border-dashed border-[#DBE5F5] rounded-2xl p-6 text-center hover:border-[#1D4ED8] transition-colors bg-[#F8FAFC]/50">
            <input
              type="file"
              accept=".csv,text/csv"
              onChange={handleFileUpload}
              className="hidden"
              id="csv-file-upload"
            />
            <label htmlFor="csv-file-upload" className="cursor-pointer block space-y-2">
              <div className="w-12 h-12 mx-auto rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center">
                <FileText className="w-6 h-6" />
              </div>
              <p className="text-sm font-bold text-[#0B1F4B]">
                Choose a CSV file from your computer
              </p>
              <p className="text-xs text-[#52627F]">
                Or click "Load Demo Dataset" above for an instant simulation
              </p>
            </label>
          </div>

          {/* Validation & Preview Section */}
          {hasValidated && (
            <div className="space-y-3">
              <div className="flex items-center justify-between">
                <h4 className="text-xs font-bold text-[#0B1F4B] uppercase tracking-wider">
                  Data Validation Results ({parsedRows.length} Rows Ingested)
                </h4>
                <div className="flex items-center gap-2 text-xs">
                  <span className="px-2.5 py-0.5 rounded-full bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] font-bold">
                    {newCount} New Batches
                  </span>
                  <span className="px-2.5 py-0.5 rounded-full bg-white text-[#0B1F4B] border border-[#DBE5F5] font-bold">
                    {updateCount} Updates
                  </span>
                  {errorCount > 0 && (
                    <span className="px-2.5 py-0.5 rounded-full bg-[#F8FAFC] text-[#D9480F] border border-[#D9480F]/30 font-bold">
                      {errorCount} Format Errors
                    </span>
                  )}
                </div>
              </div>

              {/* Table Preview */}
              <div className="border border-[#DBE5F5] rounded-xl overflow-hidden max-h-56 overflow-y-auto">
                <table className="w-full text-left text-xs">
                  <thead className="bg-[#F8FAFC] text-[#52627F] font-bold border-b border-[#DBE5F5] sticky top-0">
                    <tr>
                      <th className="py-2.5 px-3">Row</th>
                      <th className="py-2.5 px-3">Medicine</th>
                      <th className="py-2.5 px-3">Batch #</th>
                      <th className="py-2.5 px-3">Expiry Date</th>
                      <th className="py-2.5 px-3">Stock</th>
                      <th className="py-2.5 px-3">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#DBE5F5]">
                    {parsedRows.map((r) => (
                      <tr
                        key={r.rowNum}
                        className={r.status === 'error' ? 'bg-orange-50/50' : 'hover:bg-[#F8FAFC]'}
                      >
                        <td className="py-2 px-3 font-mono text-[#52627F]">{r.rowNum}</td>
                        <td className="py-2 px-3 font-medium text-[#0B1F4B]">{r.drug_name}</td>
                        <td className="py-2 px-3 font-mono text-[#0B1F4B]">#{r.batch_no}</td>
                        <td className="py-2 px-3 text-[#52627F]">{r.expiry_date}</td>
                        <td className="py-2 px-3 font-bold text-[#0B1F4B]">{r.stock} boxes</td>
                        <td className="py-2 px-3">
                          {r.status === 'valid_new' && (
                            <span className="inline-flex items-center gap-1 text-[#1D4ED8] font-semibold text-xs">
                              <CheckCircle2 className="w-3.5 h-3.5" /> New
                            </span>
                          )}
                          {r.status === 'valid_update' && (
                            <span className="inline-flex items-center gap-1 text-[#0B1F4B] font-semibold text-xs">
                              <RefreshCw className="w-3.5 h-3.5" /> Update
                            </span>
                          )}
                          {r.status === 'error' && (
                            <span
                              className="inline-flex items-center gap-1 text-[#D9480F] font-semibold text-xs"
                              title={r.errorMessage}
                            >
                              <AlertCircle className="w-3.5 h-3.5" /> {r.errorMessage}
                            </span>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </div>

        {/* Modal Footer */}
        <div className="px-6 py-4 bg-[#F8FAFC] border-t border-[#DBE5F5] flex items-center justify-between">
          <button
            onClick={onClose}
            className="px-4 py-2 text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] hover:bg-slate-200/60 rounded-xl transition-colors cursor-pointer"
          >
            Cancel
          </button>

          <div className="flex items-center gap-3">
            {isSuccess && (
              <span className="text-xs font-bold text-[#1D4ED8] flex items-center gap-1">
                <CheckCircle2 className="w-4 h-4" /> Successfully saved! Forecasts recalculating...
              </span>
            )}
            <button
              onClick={handleConfirmImport}
              disabled={!hasValidated || parsedRows.filter((r) => r.status !== 'error').length === 0}
              className="px-5 py-2.5 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-xs transition-all cursor-pointer"
            >
              Confirm & Save Stock ({parsedRows.filter((r) => r.status !== 'error').length} Records)
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
